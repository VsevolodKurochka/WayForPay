<?php header('Access-Control-Allow-Origin: *'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<!-- <script src="js/lib/jquery.min.js"></script> -->
</head>
<body>
	<?php 
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			$customers = $_POST['customer'];
			function createHeaders($from){
				$headers  = "From: " . strip_tags($from) . "\r\n";
				$headers .= "Reply-To: ". strip_tags($from) . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html;charset=utf-8 \r\n";
				$headers .= "Content-Transfer-Encoding: 8bit \r\n";

				return $headers;
			}
			function createMessage($message_fields){
				$message 	= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
				$message .= '<html xmlns="http://www.w3.org/1999/xhtml">';
				$message .= '<head><meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/></head>';
				$message .= '<body>';
				$message .= '<table rules="all" style="border: 1px solid #999; width: 100%;" cellpadding="10">';
				foreach($message_fields as $field) {
					$message .= '<tr><td>Name:</td><td><b>'.$field['name'].'</b></td></tr>';
					$message .= '<tr><td>Email:</td><td>'.$field['email'].'</td></tr>';
					$message .= '<tr><td>Phone:</td><td>'.$field['phone'].'</td></tr>';
					$message .= '<tr><td>Birth date:</td><td>'.$field['birth_date'].'</td></tr>';
					$message .= '<tr><td>Passport:</td><td>'.$field['passport'].'</td></tr>';
					$message .= '<tr><td>Gender:</td><td>'.$field['gender'].'</td></tr>';
					$message .= '<tr style="background-color: #ddd;"><td></td><td></td></tr>';
				}
				$message .= "</table>";
				$message .= "</body></html>";

				return $message;	
			}

			
			$to 				= 'your.email@gmail.com';
			$subject 		= 'Subject';
			$sendfrom 	= 'send.from@gmail.com';
			$headers 		= createHeaders('Site Subject');
			$message 		= createMessage($customers);

			$send  	 		= mail($to, $subject, $message, $headers);

			// FORM
			include 'WayForPay.php';

			$secretKey = 'secretKey';

			$merchantAccount = 'merchantAccount';
			$merchantDomainName = 'merchantDomainName';
			$merchantTransactionSecureType = 'AUTO';
			$orderDate = strtotime(date('Y-m-d'));
			$amount = '1';
			$currency = 'USD';
			$productName = 'productName';
			$productCount = '1';
			$productPrice = $_POST['totalPrice'];

			$orderReference = rand() + rand() + rand() . '_merchantAccount';

			//$string = '$merchantAccount. ';' . $merchantDomainName';'$orderReference';'$orderDate';'$productPrice';'$currency';'$productName';'$productCount';$productPrice';
			$string = ''.$merchantAccount.';'.$merchantDomainName.';'.$orderReference.';'.$orderDate.';'.$productPrice.';'.$currency.';'.$productName.';'.$productCount.';'.$productPrice.'';
			//echo $string;
			$hash = hash_hmac("md5", $string, $secretKey);

			$data = [
				'merchantAccount' 								=> $merchantAccount, 					// Идентификатор продавца. Данное значение присваивается Вам со стороны WayForPay
				'merchantDomainName' 							=> $merchantDomainName, 				// Доменное имя веб-сайта торговца
				'merchantTransactionSecureType' 	=> $merchantTransactionSecureType,		// Тип безопасности для прохождение транзакции
				'merchantSignature'								=> $hash,								// Подпись запроса
				'orderReference' 									=> $orderReference,						// Уникальный номер заказа в системе торговца				
				'orderDate' 											=> $orderDate,							// Дата размещение заказа
				'amount' 													=> $productPrice,						// Сумма заказа
				'currency' 												=> $currency,							// Валюта заказа
				'productName' 										=> $productName,
				'productCount' 										=> $productCount,
				'productPrice'										=> $productPrice,
				'merchantAuthType'								=> 'SimpleSignature'
			];

			$wfp = new WayForPay('adventuretours_in_ua' , $secretKey);
	?>
	<!-- style="display: none;" -->
	<div id="form-wayforpay" style="display: none;">
		<?php echo $wfp->buildForm($data); ?>
	</div>
	<script>
		var WayForPayForm = document.querySelector('#form-wayforpay form');
		document.addEventListener('DOMContentLoaded', function(){
			WayForPayForm.submit();
		});
	</script>
	<?php }else{ die('NO ACCESS'); }?>
</body>
</html>