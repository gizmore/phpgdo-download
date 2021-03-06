<?php
namespace GDO\Download\Method;

use GDO\Admin\MethodAdmin;
use GDO\Download\GDO_Download;
use GDO\Table\MethodQueryTable;

/**
 * Staff overview of downloads.
 * @author gizmore
 * @version 6.10
 * @since 6.04
 */
final class Admin extends MethodQueryTable
{
	use MethodAdmin;

	public function getPermission() : ?string { return 'staff'; }
	
    public function gdoTable() { return GDO_Download::table(); }

}
