<?php
namespace GDO\Download;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_Int;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Page;

/**
 * Download module with automated payment processing.
 *
 * - Paid downloads
 * - User uploads
 * - Votes and likes
 *
 * @version 7.0.2
 * @since 3.5.0
 * @see GDO_Download
 *
 * @author gizmore
 * @see Module_Payment
 */
final class Module_Download extends GDO_Module
{

	##############
	### Module ###
	##############
	public int $priority = 70;

	public function onLoadLanguage(): void { $this->loadLanguage('lang/download'); }

	public function getClasses(): array { return [GDO_Download::class, GDO_DownloadVote::class, GDO_DownloadToken::class]; }

	public function href_administrate_module(): ?string { return href('Download', 'Admin'); }

	public function getDependencies(): array
	{
		return [
			'Category',
			'File',
			'Payment',
			'Votes',
		];
	}

	public function getFriendencies(): array
	{
		return [
			'Avatar',
		];
	}

	##############
	### Config ###
	##############
	public function getConfig(): array
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

	public function onInitSidebar(): void
	{
		if ($this->cfgHookLeftBar())
		{
			$count = GDO_Download::countDownloads();
			$link = GDT_Link::make()->icon('download')->text('link_downloads', [$count])->href(href('Download', 'FileList'));
			GDT_Page::instance()->leftBar()->addField($link);
		}
	}

	public function cfgHookLeftBar() { return $this->getConfigValue('dl_hook_left_bar'); }

	public function cfgGuestUploads() { return $this->getConfigValue('dl_upload_guest'); }

	public function cfgGuestDownload() { return $this->getConfigValue('dl_download_guest'); }

	public function cfgVotesEnabled() { return $this->getConfigValue('dl_votes'); }

	public function cfgGuestVotes() { return $this->getConfigValue('dl_vote_guest'); }

	##############
	### Render ###
	##############

	public function cfgVotesOutcome() { return $this->getConfigValue('dl_votes_outcome'); }

	public function renderTabs()
	{
		$count = GDO_Download::countDownloads();
		$bar = GDT_Bar::make()->horizontal();
		$bar->addFields(
			GDT_Link::make('link_downloads')->icon('download')->href(href('Download', 'FileList'))->text('link_downloads', [$count]),
			GDT_Link::make('link_upload')->icon('upload')->href(href('Download', 'Crud')),
		);
		GDT_Page::instance()->topResponse()->addField($bar);
	}

}
