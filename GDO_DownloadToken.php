<?php
namespace GDO\Download;

use GDO\Core\GDO;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_Template;
use GDO\Core\GDT_Token;
use GDO\Payment\Orderable;
use GDO\Payment\PaymentModule;
use GDO\UI\GDT_Success;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

/**
 * Purchasable download token.
 *
 * @version 5.0
 * @since 3.0
 * @author gizmore
 */
final class GDO_DownloadToken extends GDO implements Orderable
{

	#############
	### Order ###
	#############
	public static function hasToken(GDO_User $user, GDO_Download $dl)
	{
		return self::table()->select('1')->where("dlt_user={$user->getID()} AND dlt_download={$dl->getID()}")->first()->exec()->fetchVar() === '1';
	}

	public function isPriceWithTax() { return false; }

	public function getOrderCancelURL(GDO_User $user) { return url('Download', 'FileList'); }

	public function getOrderSuccessURL(GDO_User $user) { return url('Download', 'View', 'id=' . $this->getDownloadID()); }

	public function getOrderTitle($iso) { return tiso($iso, 'card_title_downloadtoken', [html($this->getDowload()->getTitle())]); }

	/**
	 * @return GDO_Download
	 */
	public function getDowload() { return $this->gdoValue('dlt_download'); }

	public function getOrderPrice() { return $this->getDowload()->getPrice(); }

	###########
	### GDO ###
	###########

	public function canPayOrderWith(PaymentModule $module) { return true; }

	public function onOrderPaid()
	{
		$this->insert();
		return GDT_Success::with('msg_download_purchased');
	}

	public function renderOrderCard()
	{
		return $this->renderCard();
	}

	public function renderCard(): string
	{
		return GDT_Template::php('Download', 'card/download_token.php', ['gdo' => $this]);
	}

	public function gdoCached(): bool { return false; }

	public function gdoColumns(): array
	{
		return [
			GDT_User::make('dlt_user')->primary(),
			GDT_Object::make('dlt_download')->table(GDO_Download::table())->primary(),
			GDT_Token::make('dlt_token')->notNull(),
			GDT_CreatedAt::make('dlt_created'),
			GDT_CreatedBy::make('dlt_creator'),
		];
	}

	/**
	 * @return GDO_User
	 */
	public function getUser() { return $this->gdoValue('dlt_user'); }

	public function getUserID() { return $this->gdoVar('dlt_user'); }

	public function getDowloadID() { return $this->gdoVar('dlt_download'); }

	/**
	 * @return GDO_User
	 */
	public function getCreatedBy() { return $this->gdoValue('dlt_creator'); }

	##############
	### Render ###
	##############

	public function getCreatedAt() { return $this->gdoVar('dlt_created'); }

	public function getToken() { return $this->gdoVar('dlt_token'); }

}
