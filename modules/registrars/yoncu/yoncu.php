<?php
if(!function_exists('json_decode')){
	echo "Sunucunuza PHP Json Fonksiyonu Yukleyiniz.";
	exit;
}

if(isset($_REQUEST['cron'])){
	require("../../../init.php");
	require("../../../includes/functions.php");
	require("../../../includes/registrarfunctions.php");
}
$params = getRegistrarConfigOptions('yoncu');
if(!is_dir($_SERVER["DOCUMENT_ROOT"].'/tmp/')){
	mkdir($_SERVER["DOCUMENT_ROOT"].'/tmp/',0777);
}
$TmpSession	= $_SERVER["DOCUMENT_ROOT"].'/tmp/YoncuWhmResigrerPrice.json';
if(is_file($TmpSession) and filectime($TmpSession) > (time()-$params['TldListAutoUp'])){
	$TldCek	= false;
}elseif(!is_numeric($params['TldListAutoUp']) or !isset($params['TldListAutoUp']) or !isset($params['ApiUserKey']) or $params['ApiUserKey']==""){
	$TldCek	= false;
}else{
	$TldCek	= true;
}
if(isset($_REQUEST['cron']) or $TldCek == true){
	$BilgiYaz	= null;
	$Baglan	= mysqli_connect((isset($GLOBALS['db_host'])?$GLOBALS['db_host']:$whmcsAppConfig->db_host),(isset($GLOBALS['db_username'])?$GLOBALS['db_username']:$whmcsAppConfig->db_username),(isset($GLOBALS['db_password'])?$GLOBALS['db_password']:$whmcsAppConfig->db_password),(isset($GLOBALS['db_name'])?$GLOBALS['db_name']:$whmcsAppConfig->db_name));
	$SitePara	= mysqli_fetch_object(mysqli_query($Baglan,"SELECT * FROM `tblcurrencies` WHERE `default`=1"));
	if($_REQUEST['cron'] == 'UpTLD' or $TldCek == true){
		list($Durum,$Bilgi)	= yoncu_getcurlpage('uzantilar',$params,array('para'=>$SitePara->code),0);
		if($Durum){
			file_put_contents($TmpSession,time());
			chmod($TmpSession, 0777);
			foreach($Bilgi as $Uzanti => $UzantiBilgi){
				$BilgiYaz .= $Uzanti;
				$AutoUpDisable	= (isset($params['AutoUpDisable'])?explode(',',$params['AutoUpDisable']):[]);
				if(!in_array($Uzanti,$AutoUpDisable) and !in_array('.'.$Uzanti,$AutoUpDisable)){
					if($UzantiBilgi->kayit_indirim_bitis > time() and $UzantiBilgi->kayit_ucreti_indirim > 0){
						$UzantiBilgi->kayit_ucreti	= ($UzantiBilgi->kayit_ucreti/100)*(100-$UzantiBilgi->kayit_ucreti_indirim);
					}
					if($params['UcretEkYuzde'] > 0){
						$UzantiBilgi->kayit_ucreti		= ($UzantiBilgi->kayit_ucreti/100)*(100+$params['UcretEkYuzde']);
						$UzantiBilgi->uzatma_ucreti		= ($UzantiBilgi->uzatma_ucreti/100)*(100+$params['UcretEkYuzde']);
						$UzantiBilgi->transfer_ucreti	= ($UzantiBilgi->transfer_ucreti/100)*(100+$params['UcretEkYuzde']);
					}
					if($params['UcretEkFiyat'] > 0){
						$UzantiBilgi->kayit_ucreti		+= $params['UcretEkFiyat'];
						$UzantiBilgi->uzatma_ucreti		+= $params['UcretEkFiyat'];
						$UzantiBilgi->transfer_ucreti	+= $params['UcretEkFiyat'];
					}
					if(!isset($UzantiBilgi->uzatma_ucreti) or $UzantiBilgi->uzatma_ucreti < 1){
						$UzantiBilgi->uzatma_ucreti	= $UzantiBilgi->kayit_ucreti;
					}
					if(!isset($UzantiBilgi->transfer_ucreti) or $UzantiBilgi->transfer_ucreti < 1){
						$UzantiBilgi->transfer_ucreti	= $UzantiBilgi->kayit_ucreti;
					}
					$DbUzId=0;
					$DbUzx	= mysqli_query($Baglan,"SELECT * FROM `tbldomainpricing` WHERE `extension`='.".$Uzanti."'");
					$DbUzs	= mysqli_num_rows($DbUzx);
					if($DbUzs == 1){
						$DbUz	= mysqli_fetch_object($DbUzx);
						$DbUzId	= $DbUz->id;
					}elseif($DbUzs == 0){
						mysqli_query($Baglan,"INSERT INTO `tbldomainpricing` (`id`, `extension`, `dnsmanagement`, `emailforwarding`, `idprotection`, `eppcode`, `autoreg`, `order`) VALUES (NULL, '.".$Uzanti."', '', '', '', '', 'yoncu', '0')");
						$DbUzId	= mysqli_insert_id($Baglan);
					}else{
						$BilgiYaz .= ' Uzantıdan Veritabanında Birden Fazla Var. Fazlasını Silmelisiniz.';
					}
					if($DbUzId != 0){
						if($UzantiBilgi->kayit_ucreti > 0){
							$DbPricex	= mysqli_query($Baglan,"SELECT * FROM `tblpricing` WHERE `type`='domainregister' and `relid`='".$DbUzId."' and currency='".$SitePara->id."'");
							$DbPrices	= mysqli_num_rows($DbPricex);
							if($DbPrices == 1){
								$DbPrice	= mysqli_fetch_object($DbPricex);
								mysqli_query($Baglan,"UPDATE `tblpricing` SET `msetupfee`='".$UzantiBilgi->kayit_ucreti."',`qsetupfee`='".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*1)),2)."',`ssetupfee`='".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*2)),2)."',`asetupfee`='".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*3)),2)."',`bsetupfee`='".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*4)),2)."',`tsetupfee`='0.00',`monthly`='".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*5)),2)."',`quarterly`='".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*6)),2)."',`semiannually`='".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*7)),2)."',`annually`='".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*8)),2)."',`biennially`='".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*9)),2)."',`triennially`='0.00' where `id` = ".$DbPrice->id);
							}elseif($DbPrices == 0){
								mysqli_query($Baglan,"INSERT INTO `tblpricing` (`id`, `type`, `currency`, `relid`, `msetupfee`, `qsetupfee`, `ssetupfee`, `asetupfee`, `bsetupfee`, `tsetupfee`, `monthly`, `quarterly`, `semiannually`, `annually`, `biennially`, `triennially`) VALUES (NULL, 'domainregister', '".$SitePara->id."', '".$DbUzId."', '".$UzantiBilgi->kayit_ucreti."','".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*1)),2)."', '".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*2)),2)."', '".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*3)),2)."', '".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*4)),2)."', '0.00', '".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*5)),2)."', '".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*6)),2)."', '".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*7)),2)."', '".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*8)),2)."', '".round(($UzantiBilgi->kayit_ucreti+($UzantiBilgi->uzatma_ucreti*9)),2)."', '0.00')");
							}else{
								mysqli_query($Baglan,"DELETE FROM `tblpricing` WHERE `type`='domainregister' and `relid`='".$DbUzId."' and currency='".$SitePara->id."'");
							}
							$BilgiYaz .= ' REG:'.$DbPrices;
						}
						if($UzantiBilgi->uzatma_ucreti > 0){
							$DbPricex	= mysqli_query($Baglan,"SELECT * FROM `tblpricing` WHERE `type`='domainrenew' and `relid`='".$DbUzId."' and currency='".$SitePara->id."'");
							$DbPrices	= mysqli_num_rows($DbPricex);
							if($DbPrices == 1){
								$DbPrice	= mysqli_fetch_object($DbPricex);
								mysqli_query($Baglan,"UPDATE `tblpricing` SET `msetupfee`='".$UzantiBilgi->uzatma_ucreti."',`qsetupfee`='".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*1)),2)."',`ssetupfee`='".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*2)),2)."',`asetupfee`='".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*3)),2)."',`bsetupfee`='".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*4)),2)."',`tsetupfee`='0.00',`monthly`='".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*5)),2)."',`quarterly`='".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*6)),2)."',`semiannually`='".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*7)),2)."',`annually`='".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*8)),2)."',`biennially`='".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*9)),2)."',`triennially`='0.00' where `id` = ".$DbPrice->id);
								mysqli_query($Baglan,"UPDATE `tbldomains` SET `recurringamount`='".$UzantiBilgi->uzatma_ucreti."' where `registrar` = 'yoncu' and `domain` REGEXP '^([a-zA-Z-]+.".$Uzanti.")\$';");
							}elseif($DbPrices == 0){
								mysqli_query($Baglan,"INSERT INTO `tblpricing` (`id`, `type`, `currency`, `relid`, `msetupfee`, `qsetupfee`, `ssetupfee`, `asetupfee`, `bsetupfee`, `tsetupfee`, `monthly`, `quarterly`, `semiannually`, `annually`, `biennially`, `triennially`) VALUES (NULL, 'domainrenew', '".$SitePara->id."', '".$DbUzId."', '".$UzantiBilgi->uzatma_ucreti."','".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*1)),2)."', '".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*2)),2)."', '".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*3)),2)."', '".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*4)),2)."', '0.00', '".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*5)),2)."', '".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*6)),2)."', '".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*7)),2)."', '".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*8)),2)."', '".round(($UzantiBilgi->uzatma_ucreti+($UzantiBilgi->uzatma_ucreti*9)),2)."', '0.00')");
							}else{
								mysqli_query($Baglan,"DELETE FROM `tblpricing` WHERE `type`='domainrenew' and `relid`='".$DbUzId."' and currency='".$SitePara->id."'");
							}
							$BilgiYaz .= ' RNW:'.$DbPrices;
						}
						if($UzantiBilgi->transfer_ucreti > 0){
							$DbPricex	= mysqli_query($Baglan,"SELECT * FROM `tblpricing` WHERE `type`='domaintransfer' and `relid`='".$DbUzId."' and currency='".$SitePara->id."'");
							$DbPrices	= mysqli_num_rows($DbPricex);
							if($DbPrices == 1){
								$DbPrice	= mysqli_fetch_object($DbPricex);
								mysqli_query($Baglan,"UPDATE `tblpricing` SET `msetupfee`='".$UzantiBilgi->transfer_ucreti."',`qsetupfee`='".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*1)),2)."',`ssetupfee`='".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*2)),2)."',`asetupfee`='".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*3)),2)."',`bsetupfee`='".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*4)),2)."',`tsetupfee`='0.00',`monthly`='".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*5)),2)."',`quarterly`='".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*6)),2)."',`semiannually`='".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*7)),2)."',`annually`='".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*8)),2)."',`biennially`='".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*9)),2)."',`triennially`='0.00' where `id` = ".$DbPrice->id);
							}elseif($DbPrices == 0){
								mysqli_query($Baglan,"INSERT INTO `tblpricing` (`id`, `type`, `currency`, `relid`, `msetupfee`, `qsetupfee`, `ssetupfee`, `asetupfee`, `bsetupfee`, `tsetupfee`, `monthly`, `quarterly`, `semiannually`, `annually`, `biennially`, `triennially`) VALUES (NULL, 'domaintransfer', '".$SitePara->id."', '".$DbUzId."', '".$UzantiBilgi->transfer_ucreti."','".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*1)),2)."', '".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*2)),2)."', '".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*3)),2)."', '".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*4)),2)."', '0.00', '".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*5)),2)."', '".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*6)),2)."', '".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*7)),2)."', '".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*8)),2)."', '".round(($UzantiBilgi->transfer_ucreti+($UzantiBilgi->uzatma_ucreti*9)),2)."', '0.00')");
							}else{
								mysqli_query($Baglan,"DELETE FROM `tblpricing` WHERE `type`='domaintransfer' and `relid`='".$DbUzId."' and currency='".$SitePara->id."'");
							}
							$BilgiYaz .= ' TRSF:'.$DbPrices;
						}
						if(mysqli_errno($Baglan) != 0){
							$BilgiYaz .= " DB Hata: ".mysqli_errno($Baglan)." - ".mysqli_error($Baglan);
							break;
						}
					}
					mysqli_query($Baglan,"UPDATE `tbldomainpricing` SET `dnsmanagement`=1 WHERE `extension`='.".$Uzanti."'");
				}else{
					$BilgiYaz .= ' Güncellemelere Kapalı';
				}
				$BilgiYaz .= "\n";
			}
		}else{
			$BilgiYaz .= 'Uzantı Listesi Alınamadı. Hata: '.$Bilgi;
		}
	}
	if(isset($_REQUEST['cron'])){
		echo '<pre>'.$BilgiYaz;
	}
}

function yoncu_getconfigarray(){
	$Urlx='http'.($_SERVER["SERVER_PORT"]==443||$_SERVER["HTTP_HTTPSSL"]=='true'?'s':null).'://'.$_SERVER["SERVER_NAME"].dirname(dirname($_SERVER["SCRIPT_NAME"]));
	$UyeBilgileri	= array(
		'ApiUserID'	=> array(
			'FriendlyName'	=> "API ID",
			'Type'			=> 'text',
			'Size'			=> '15',
			'Description'	=> '<br/>Bu Bilgiye "Üye İşlemleri / Menü Devamı / Güvenlik ayarları / API Erişim" Menüsünden Ulaşabilirsiniz',
			'Default'		=> "",
		),
		'ApiUserKey'	=> array(
			'FriendlyName'	=> "API Key",
			'Type'			=> 'text',
			'Size'			=> '55',
			'Description'	=> '<br/>Bu Bilgiye "Üye İşlemleri / Menü Devamı / Güvenlik ayarları / API Erişim" Menüsünden Ulaşabilirsiniz',
			'Default'		=> "",
		),
		'PromosyonKodu'	=> array(
			'FriendlyName'	=> "İndirim Kodu",
			'Type'			=> 'text',
			'Size'			=> '36',
			'Description'	=> 'Size özel bir indirim kodu verdi ise belirtiniz',
			'Default'		=> "",
		),
		'TestMode'	=> array(
			'FriendlyName'	=> "Test Modu",
			'Type'			=> 'yesno',
			'Description'	=> 'Test Modunda Alan Adı Kayıt Edilmez Fakat Kayıt Edildi Görünür',
		),
		'UcretEkYuzde'	=> array(
			'FriendlyName'	=> "Kar Yüzdesi",
			'Type'			=> 'text',
			'Size'			=> '15',
			'Default'		=> "1",
			'Description'	=> 'Ücrete Eklenecek Yüzde Oranında Kar Payı',
		),
		'UcretEkFiyat'	=> array(
			'FriendlyName'	=> "Kar Ücreti",
			'Type'			=> 'text',
			'Size'			=> '15',
			'Default'		=> "3",
			'Description'	=> 'Ücrete Eklenecek Kar Fiyatı',
		),
		'TldListAutoUp'	=> array(
			'FriendlyName'	=> "Otomatik Güncelleme",
			'Type'			=> 'text',
			'Size'			=> '15',
			'Default'		=> "86400",
			'Description'	=> 'Saniye - Default: 86400 (86400:1 Gün,x=İptal)<br/><a target="_blank" href="../modules/registrars/yoncu/yoncu.php?cron=UpTLD">Buraya</a> tıklayarak uzantı listesini ve fiatları hemen güncelleyebilirsiniz.<br/>Cron Önerisi:<br/><input disabled style="width: 100%;" value=\'0 7 * * * "curl -s '.$Urlx.'/modules/registrars/yoncu/yoncu.php?cron=UpTLD"\'/>',
		),
		'AutoUpDisable'	=> array(
			'FriendlyName'	=> "Güncellenmeyecek Uzantılar",
			'Type'			=> 'text',
			'Size'			=> '55',
			'Default'		=> "",
			'Description'	=> '<br>Fiyat ve özelliklerin otomatik güncellenmesini istemediğiniz uzantılar var ise buraya virgül ile ayırarak yazabilirsiniz.<br>Örnek: biz,org,com.tr,de,tk',
		),
	);
	return $UyeBilgileri;
}
function yoncu_getcurlpage($Islem,$params,$PostVeri=array(),$Deneme=0){
	if(empty($params['ApiUserID']) or empty($params['ApiUserKey']) or !is_numeric($params['ApiUserID']) or !($params['ApiUserID'] > 0)){
		return array(false,'API Login Bilgileri Hatalı');
	}
	$PostVeri['ka']	= $params['ApiUserID'];
	$PostVeri['sf']	= $params['ApiUserKey'];
	$PostVeri['id']	= $params['ApiUserID'];
	$PostVeri['key']= $params['ApiUserKey'];
	$Post	= array();
	foreach($PostVeri as $Adi => $Veri){
		$Post[]	= $Adi.'='.(empty($Veri)?'':urlencode($Veri));
	}
	$URL	= 'http://www.yoncu.com/apiler/domain/'.$Islem.'.php';
	$ch = curl_init ();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir().DIRECTORY_SEPARATOR.'yoncu.com');
	curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir().DIRECTORY_SEPARATOR.'yoncu.com');
	curl_setopt($ch, CURLOPT_USERAGENT, 'WHMCS DomainMod '.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	curl_setopt($ch, CURLOPT_REFERER, $URL);
	curl_setopt($ch, CURLOPT_URL,'https://www.yoncu.com/YoncuTest/YoncuSec_Token');
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Cookie: YoncuKoruma='.$_SERVER['SERVER_ADDR'].';YoncuKorumaRisk=0;']);
	$Token = trim(curl_exec($ch));
	if(strlen($Token) != 32){
		return array(false,'Token Alınamadı');
	}
	curl_setopt($ch, CURLOPT_URL,$URL);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Cookie: YoncuKoruma='.$_SERVER['SERVER_ADDR'].';YoncuKorumaRisk=0;YoncuSec='.$Token]);
	curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&',$Post));
	$Json = curl_exec($ch);
	$HttpStatus	= curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if($HttpStatus != 200){
		if($Deneme < 4){
			sleep(3);
			return yoncu_getcurlpage($Islem,$params,$PostVeri,($Deneme+1));
		}
		return array(false,'Veri Çekilemedi. Status: '.$HttpStatus);
	}elseif(trim($Json) != ""){
		return json_decode($Json);
	}else{
		return array(false,'Veri Boş Geldi');
	}
	curl_close($ch);
}
function yoncu_getnameservers($params){
	$YoncuBilgi = yoncu_getcurlpage('get/bilgi',$params,array('aa'=>$params['sld'].'.'.$params['tld']));
	if($YoncuBilgi[0] == true){
		$values['ns1'] = $YoncuBilgi[1][0]->dns->ns1;
		$values['ns2'] = $YoncuBilgi[1][0]->dns->ns2;
		if(isset($YoncuBilgi[1][0]->dns->ns3)) $values['ns3'] = $YoncuBilgi[1][0]->dns->ns3;
		if(isset($YoncuBilgi[1][0]->dns->ns4)) $values['ns4'] = $YoncuBilgi[1][0]->dns->ns4;
		if(isset($YoncuBilgi[1][0]->dns->ns5)) $values['ns5'] = $YoncuBilgi[1][0]->dns->ns5;
		if(isset($YoncuBilgi[1][0]->dns->ns6)) $values['ns6'] = $YoncuBilgi[1][0]->dns->ns6;
	}else{
		$values['error'] = $YoncuBilgi[1];
	}
	return $values;
}
function yoncu_savenameservers($params){
	$params['aa']=$params['sld'].'.'.$params['tld'];
	$YoncuBilgi = yoncu_getcurlpage('get/guncelle',$params,$params);
	if($YoncuBilgi[0] == true){
		$values	= true;
	}else{
		$values['error'] = $YoncuBilgi[1];
	}
	return $values;
}
function yoncu_getcontactdetails($params){
	$PostVeri	= array(
		'aa'			=> $params['sld'].'.'.$params['tld'],
	);
	$YoncuBilgi = yoncu_getcurlpage('get/bilgi',$params,$PostVeri);
	if($YoncuBilgi[0] == true){
		$IletisimGuncellemeGerekenler	= array(
			'adi_soyadi'	=> 'Ad Soyad',
			'firma_adi'		=> 'Firma',
			'adres1'		=> 'Adres 1',
			'adres2'		=> 'Adres 2',
			'adres3'		=> 'Adres 3',
			'posta_kodu'	=> 'Posta Kodu',
			'sehir'			=> 'Sehir',
			'ulke'			=> 'Ulke',
			'ilce'			=> 'ilce',
			'ulke_tel_kodu'	=> 'Ulke Tel Kodu',
			'telefon'		=> 'Telefon No',
			'ulke_fax_kodu'	=> 'Ulke Faks Kod',
			'faks'			=> 'Faks No',
			'mail_adresi'	=> 'Mail Adresi',
		);
		foreach($IletisimGuncellemeGerekenler as $adi=>$aci){
			$values['Domain Kayit Edici Bilgileri'][$aci]	= $YoncuBilgi[1][0]->iletisim->$adi;
		}
	}else{
		$values['error'] = $YoncuBilgi[1];
	}
	return $values;
}
function yoncu_savecontactdetails($params){
	$IletisimGuncellemeGerekenler	= array(
		'adi_soyadi'	=> 'Ad Soyad',
		'firma_adi'		=> 'Firma',
		'adres1'		=> 'Adres 1',
		'adres2'		=> 'Adres 2',
		'adres3'		=> 'Adres 3',
		'posta_kodu'	=> 'Posta Kodu',
		'sehir'			=> 'Sehir',
		'ulke'			=> 'Ulke',
		'ilce'			=> 'ilce',
		'ulke_tel_kodu'	=> 'Ulke Tel Kodu',
		'telefon'		=> 'Telefon No',
		'ulke_fax_kodu'	=> 'Ulke Faks Kod',
		'faks'			=> 'Faks No',
		'mail_adresi'	=> 'Mail Adresi',
	);
	$PostVeri	= array(
		'aa'			=> $params['sld'].'.'.$params['tld'],
		'is'			=> 'iletisim',
	);
	foreach($IletisimGuncellemeGerekenler as $adi=>$aci){
		$PostVeri[$adi]	= $params['contactdetails']['Domain Kayit Edici Bilgileri'][$aci];
	}
	$YoncuBilgi = yoncu_getcurlpage('get/guncelle',$params,$PostVeri);
	if($YoncuBilgi[0] == true){
		$values	= true;
	}else{
		$values['error'] = $YoncuBilgi[1];
		echo '<script> alert("'.$YoncuBilgi[1].'"); </script>';
	}
	return $values;
}
function yoncu_registerdomain($params){
	$PostVeri	= array();
	if(strlen(@$params['PromosyonKodu']) == 32){
		$PostVeri['pk']	= $params['PromosyonKodu'];
	}
	if(@$params['TestMode'] != ""){
		$PostVeri['test']	= 1;
	}
	$PostVeri['aa']	= $params['sld'].'.'.$params['tld'];
	$PostVeri['yl']	= $params['regperiod'];
	$YoncuBilgi = yoncu_getcurlpage('get/kayit',$params,$PostVeri);
	if($YoncuBilgi[0] == true){
		$values	= true;
		if(!isset($PostVeri['test'])){
			yoncu_getnameservers($params['original']);
		}
	}else{
		$values['error'] = $YoncuBilgi[1];
	}
	return $values;
}
?>
