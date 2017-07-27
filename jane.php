<?php
require_once('config.php');
require('quickstart.php');
$days = unserialize (DAYS);
$months = unserialize (MONTHS);
$options=getopt('',array('cc::'));
if (isset($options["cc"])){
	if ($options["cc"])
		$month=$options["cc"];
	else
		$month=date("n");

	$year=date("Y");
	$days_of_month=date("d",mktime(0,0,0,$month+1,0,$year));

	$dateOne=$months[$month]." 1 del ".$year;
	$dateTwo=$months[$month]." $days_of_month del ".$year;

	$currentDate=$days[date('w')]." ".date('d')." de ".$months[date('n')]. " - ".date('Y');
	# Get Template
	$templateId = getenv('TEMPLATE_ID');
	$response = $service->files->export($templateId,'application/vnd.oasis.opendocument.text', array(
		  'alt' => 'media' ));
	$content = $response->getBody()->getContents();
	$fileTemplate=fopen("/tmp/template.odt","a") or die("Problemas en la creacion");
	fputs($fileTemplate,$content);
	fclose($fileTemplate);
	$pageToken = null;
	do {
		$response = $service->files->listFiles(array(
			'q' => "trashed=false and '".DRIVE_FOLDER_ID."' in parents",
			'orderBy' => 'createdTime desc',
			'corpus' => 'user',
			'fields' => 'files(id,name)'
		));
		foreach ($response->getFiles() as $file) {
			if($file->name==$year){
				$yearFolderId = $file->id;
				break;
			}
		}
	} while ($pageToken != null);
	if (!$yearFolderId){
		$fileMetadata = new Google_Service_Drive_DriveFile(array(
			'name' => $year,
			'mimeType' => 'application/vnd.google-apps.folder',
			'parents' => array(DRIVE_FOLDER_ID)
		));
		$file = $service->files->create($fileMetadata, array(
			'fields' => 'id'));
		$yearFolderId = $file->id;
	}
	do {
		$response = $service->files->listFiles(array(
			'q' => "trashed=false and '$yearFolderId' in parents",
			'orderBy' => 'createdTime desc',
			'corpus' => 'user',
			'fields' => 'files(id,name)'
		));
		foreach ($response->getFiles() as $file) {
			if($file->name==$months[$month]){
				$monthFolderId = $file->id;
				break;
			}
		}
	} while ($pageToken != null);
	if (!$monthFolderId){
		$fileMetadata = new Google_Service_Drive_DriveFile(array(
			'name' => $months[$month],
			'mimeType' => 'application/vnd.google-apps.folder',
			'parents' => array($yearFolderId)
		));
		$file = $service->files->create($fileMetadata, array(
			'fields' => 'id'));
		$monthFolderId = $file->id;
	}
	$title="CC - ".$months[$month]."/$year";
	$fileMetadata = new Google_Service_Drive_DriveFile(array(
		'name' => "$title.pdf",
		'parents' => array($monthFolderId)
	));
	# Get SS
	do {
		$response = $service->files->listFiles(array(
			'q' => "trashed=false and '$monthFolderId' in parents",
			'orderBy' => 'createdTime desc',
			'corpus' => 'user',
			'fields' => 'files(id,name)'
		));
		foreach ($response->getFiles() as $file) {
				if ($file->name=="SS - ".$months[$month]."/$year.pdf"){
					$response = $service->files->get($file->id, array(
		  'alt' => 'media' ));
					$content = $response->getBody()->getContents();
					$fileSs=fopen("/tmp/SS.pdf","a") or die("Problemas en la creacion");
					fputs($fileSs,$content);
					fclose($fileSs);
					$text=\Spatie\PdfToText\Pdf::getText('/tmp/SS.pdf');
					$ub=strpos($text,'TIPO DE PLANILLA');
					$ss=substr($text,$ub-10,10); 
					break;
				}
		}
	} while ($pageToken != null);
	# Horas
	$receivable=getenv('RECEIVABLE');
	$receivableOnText=NumeroALetras::convertir($receivable);
	$receivable=number_format($receivable);
	$in=exec('./combiner.sh "'.$month.'" "'.$currentDate.'" "'.$dateOne.'" "'.$dateTwo.'" "'.$receivableOnText.'" "'.$receivable.'" "'.$ss.'" "'.$months[$month].'"',$out);

	$templateId = getenv('TEMPLATE_ID');
	$response = $service->files->export($templateId,'application/vnd.oasis.opendocument.text', array(
		  'alt' => 'media' ));
	$content = $response->getBody()->getContents();
	$fileTemplate=fopen("/tmp/template.odt","a") or die("Problemas en la creacion");
	fputs($fileTemplate,$content);
	fclose($fileTemplate);
	$content = file_get_contents("/tmp/charge_account.pdf");
	$file = $service->files->create($fileMetadata, array(
		'data' => $content,
		'mimeType' => 'application/pdf',
		'uploadType' => 'multipart',
		'fields' => 'id'));
	echo $title;
}
?>
