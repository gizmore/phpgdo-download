<?php
use GDO\Download\GDO_Download;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;

$bar = GDT_Bar::make()->horizontal();
$count = GDO_Download::countDownloads();
$bar->addFields(
	GDT_Link::make('link_downloads')->icon('download')->href(href('Download', 'FileList'))->text('link_downloads', [$count]),
	GDT_Link::make('link_upload')->icon('upload')->href(href('Download', 'Crud')),
);
echo $bar->renderHTML();
