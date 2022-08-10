<?php
namespace GDO\Download;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Int;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Page;

/**
 * Download module with automated payment processing.
 * 
 * - Paid downloads
 * - User uploads
 * - Votes and likes
 * 
 * @author gizmore
 * @see Module_Payment
 * @see GDO_Download
 * 
 * @version 6.10.6
 * @since 3.5.0
 */
final class Module_Download extends GDO_Module
{
	##############
	### Module ###
	##############
	public int $priority = 70;
	public function onLoadLanguage() : void { $this->loadLanguage('lang/download'); }
	public function getClasses() : array { return [GDO_Download::class, GDO_DownloadVote::class, GDO_DownloadToken::class]; }
	public function hrefAdministration() : ?string { return href('Download', 'Admin'); }
	
	public function getDependencies() : array
	{
		return [
			'Payment',
		];
	}

	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			GDT_Checkbox::make('dl_upload_guest')->initial('1'),
			GDT_Checkbox::make('dl_download_guest')->initial('1'),
			GDT_Checkbox::make('dl_votes')->initial('1'),
		    GDT_Checkbox::make('dl_vote_guest')->initial('1'),
		    GDT_Checkbox::make('dl_hook_left_bar')->initial('1'),
		    GDT_Int::make('dl_votes_outcome')->unsigned()->initial('1'),
		];
	}
	public function cfgGuestUploads() { return $this->getConfigValue('dl_upload_guest'); }
	public function cfgGuestDownload() { return $this->getConfigValue('dl_download_guest'); }
	public function cfgVotesEnabled() { return $this->getConfigValue('dl_votes'); }
	public function cfgGuestVotes() { return $this->getConfigValue('dl_vote_guest'); }
	public function cfgHookLeftBar() { return $this->getConfigValue('dl_hook_left_bar'); }
	public function cfgVotesOutcome() { return $this->getConfigValue('dl_votes_outcome'); }

	##############
	### Render ###
	##############
	public function renderTabs()
	{
	    GDT_Page::$INSTANCE->topBar()->addField(
	        $this->templatePHP('tabs.php')
        );
	}
	
	public function onInitSidebar() : void
	{
	    if ($this->cfgHookLeftBar())
	    {
	        $count = GDO_Download::countDownloads();
	        $link = GDT_Link::make()->label('link_downloads', [$count])->href(href('Download', 'FileList'));
	        GDT_Page::instance()->leftBar()->addField($link);
	    }
	}

}
