<?php
namespace GDO\Download\Method;

use GDO\Core\GDT_Hook;
use GDO\Core\Method;
use GDO\Download\GDO_Download;
use GDO\User\GDO_User;
use GDO\File\Method\GetFile;
use GDO\Core\GDT_Object;

/**
 * Download a download. 
 * @author gizmore
 * @version 7.0.1
 */
final class File extends Method
{
    public function gdoParameters() : array
    {
        return [
            GDT_Object::make('id')->table(GDO_Download::table())->notNull(),
        ];
    }
    
    public function getMethodTitle() : string
    {
    	return $this->getDownload()->displayTitle();
    }
    	
    protected function getDownload() : GDO_Download
    {
    	return $this->gdoParameterValue('id');
    }
    
	public function execute()
	{
		$user = GDO_User::current();
		$download = $this->getDownload();
		if (!$download->canDownload($user))
		{
			GDO_Download::notFoundException($download->getID());
		}
		
		$download->increase('dl_downloads');
		
		GDT_Hook::callHook('DownloadFile', $user, $download);
		
		return GetFile::make()->executeWithId($download->getFileID());
	}

}
