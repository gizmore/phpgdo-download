<?php 
namespace GDO\Download\tpl\card;
/** @var $gdo \GDO\Download\GDO_DownloadToken **/
$dl = $gdo->getDowload();
$file = $dl->getFile(); ?>
<md-card class="gdo-downloadtoken">
  <md-card-title>
	<md-card-title-text>
	  <span class="md-headline">
		<div><?= t('card_title_downloadtoken', [html($dl->getTitle())]); ?></div>
		<div class="gdo-card-subtitle"><?= t('card_title_downloadprice', [$dl->displayPrice()]); ?></div>
	  </span>
	</md-card-title-text>
  </md-card-title>
  <gdo-div></gdo-div>
  <md-card-content flex>
	<div><?= t('name'); ?>: <?= html($file->getName()); ?></div>
	<div><?= t('type'); ?>: <?= $file->getType(); ?></div>
	<div><?= t('size'); ?>: <?= $file->displaySize(); ?></div>
	<div><?= t('downloads'); ?>: <?= $dl->getDownloads(); ?></div>
	<div><?= t('votes'); ?>: <?= $dl->gdoColumn('dl_votes')->renderHTML(); ?></div>
	<div><?= t('rating'); ?>: <?= $dl->gdoColumn('dl_rating')->renderHTML(); ?></div>
	<div><?= t('price'); ?>: <?= $dl->displayPrice(); ?></div>
  </md-card-content>
</md-card>
