<?php
namespace GDO\Download\Method;

use GDO\Core\GDO;
use GDO\Core\GDO_Error;
use GDO\Core\GDT_Response;
use GDO\Download\GDO_Download;
use GDO\Download\Module_Download;
use GDO\User\GDO_User;
use GDO\UI\MethodCard;
use GDO\UI\GDT_CardView;

/**
 * View a download for downloading or purchase.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 3.1.0
 */
final class View extends MethodCard
{
	public function gdoTable() : GDO
	{
		return GDO_Download::table();
	}
	
	public function beforeExecute() : void
	{
		$module = Module_Download::instance();
		$module->renderTabs();
	}
	
	protected function getDownload() : ?GDO_Download
	{
		return $this->getObject();
	}
	
	public function getMethodTitle() : string
	{
		$dl = $this->getDownload();
		return t('mt_download_view', [$dl->displayTitle()]);
	}

	public function execute()
	{
		# File
		$dl = $this->getDownload();

		# Security
		$user = GDO_User::current();
		if (!$dl->canView($user))
		{
			throw new GDO_Error('err_gdo_not_found',
				[
					$dl->gdoHumanName(),
					$dl->getID()
				]);
		}
		
		# Render
		return GDT_Response::makeWith(GDT_CardView::make()->gdo($dl));
	}
	
}
