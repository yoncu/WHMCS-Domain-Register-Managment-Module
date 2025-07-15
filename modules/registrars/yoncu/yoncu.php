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
if(!is_file(__DIR__."/logo.gif") or filesize(__DIR__."/logo.gif") < 100){
	file_put_contents(__DIR__."/logo.gif",base64_decode('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'));
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
function yoncu_getcurlpage($Islem,$params,$PostVeri=[],$Deneme=0){
	if(empty($params['ApiUserID']) or empty($params['ApiUserKey']) or !is_numeric($params['ApiUserID']) or !($params['ApiUserID'] > 0)){
		return array(false,'API Login Bilgileri Hatalı');
	}
	$PostVeri['ka']	= $params['ApiUserID'];
	$PostVeri['sf']	= $params['ApiUserKey'];
	$PostVeri['id']	= $params['ApiUserID'];
	$PostVeri['key']= $params['ApiUserKey'];
	$Post	= array();
	foreach($PostVeri as $Adi => $Veri){
		if(is_string($Adi)){
			if(is_object($Veri) or is_array($Veri)){
				$Veri=json_encode($Veri);
			}
			$Post[]	= $Adi.'='.(is_string($Veri)?urlencode($Veri):$Veri);
		}
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
	$YoncuBilgi = yoncu_getcurlpage('get/bilgi',$params,['aa'=>$params['sld'].'.'.$params['tld']]);
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
