<?php
namespace GDO\Download;

use GDO\Category\GDT_Category;
use GDO\DB\Cache;
use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\DB\GDT_DeletedAt;
use GDO\DB\GDT_DeletedBy;
use GDO\Core\GDT_EditedAt;
use GDO\DB\GDT_EditedBy;
use GDO\Date\GDT_DateTime;
use GDO\File\GDO_File;
use GDO\File\GDT_File;
use GDO\Payment\GDT_Money;
use GDO\Core\GDT_Template;
use GDO\UI\GDT_Message;
use GDO\User\GDT_Level;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\Votes\GDT_VoteCount;
use GDO\Votes\GDT_VoteRating;
use GDO\Votes\WithVotes;
use GDO\UI\GDT_Title;
use GDO\Core\GDT_UInt;

/**
 * A download is votable, likeable, purchasable.
 * 
 * @author gizmore
 * @version 6.10
 * @since 3.0
 * 
 * @see GDO_DownloadToken
 */
final class GDO_Download extends GDO
{
	#############
	### Votes ###
	#############
	use WithVotes;
	public function gdoVoteTable() { return GDO_DownloadVote::table(); }
	public function gdoVoteMin() { return 1; }
	public function gdoVoteMax() { return 5; }
	public function gdoVotesBeforeOutcome() { return Module_Download::instance()->cfgVotesOutcome(); }
	public function gdoVoteAllowed(GDO_User $user) { return $user->getLevel() >= $this->getLevel(); }
	
	###########
	### GDO ###
	###########
	public function gdoColumns() : array
	{
		return array(
			GDT_AutoInc::make('dl_id'),
			GDT_Title::make('dl_title')->notNull(),
			GDT_Message::make('dl_info')->label('info'),
			GDT_Category::make('dl_category'),
			GDT_File::make('dl_file')->notNull(),
			GDT_UInt::make('dl_downloads')->notNull()->initial('0')->writeable(false)->label('downloads'),
			GDT_Money::make('dl_price'),
			GDT_Level::make('dl_level')->notNull()->initial('0'),
			GDT_VoteCount::make('dl_votes'),
			GDT_VoteRating::make('dl_rating'),
			GDT_CreatedAt::make('dl_created'),
			GDT_CreatedBy::make('dl_creator'),
			GDT_DateTime::make('dl_accepted')->writeable(false)->label('accepted_at'),
			GDT_User::make('dl_acceptor')->writeable(false)->label('accepted_by'),
			GDT_EditedAt::make('dl_edited'),
			GDT_EditedBy::make('dl_editor'),
			GDT_DeletedAt::make('dl_deleted'),
			GDT_DeletedBy::make('dl_deletor'),
		);
	}
	
	##############
	### Bridge ###
	##############
	
	public function href_edit() { return $this->href_with('Crud'); }
	public function href_view() { return $this->href_with('View'); }
	public function href_download() { return $this->href_with('File'); }

	private function href_with($method) { return href('Download', $method, $this->href_appendix()); }
	private function href_appendix() { return '&title=' . seo($this->displayTitle()) . '&id=' . $this->getID(); }
	
	public function gdoHashcode() : string { return self::gdoHashcodeS($this->gdoVars(['dl_id', 'dl_title', 'dl_category', 'dl_file', 'dl_created', 'dl_creator'])); }

	public function canEdit(GDO_User $user) { return $user->hasPermission('staff'); } 
	public function canView(GDO_User $user) { return ($this->isAccepted() && (!$this->isDeleted())) || $user->isStaff(); }
	public function canDownload(GDO_User $user)
	{
		if ($this->isPaid())
		{
			if (!GDO_DownloadToken::hasToken($user, $this))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		if ($user->getLevel() < $this->getLevel())
		{
			return false;
		}
		if ($this->isDeleted() || (!$this->isAccepted()) )
		{
			return false;
		}
		return true;
	}
	
	public function canPurchase()
	{
		return $this->isPaid();
	}
	
	##############
	### Getter ###
	##############
	
	/**
	 * @return GDO_File
	 */
	public function getFile() { return $this->gdoValue('dl_file'); }
	public function getFileID() { return $this->gdoVar('dl_file'); }
	
	/**
	 * @return GDO_User
	 */
	public function getCreator() { return $this->gdoValue('dl_creator'); }
	public function getCreatorID() { return $this->gdoVar('dl_creator'); }
	public function getCreateDate() { return $this->gdoVar('dl_created'); }
	/**
	 * @return GDT_Message
	 */
	public function gdoMessage() { return $this->gdoColumn('dl_info'); }
	
	public function getDownloads() { return $this->gdoVar('dl_downloads'); }
	public function getRating() { return $this->gdoVar('dl_rating'); }
	public function getVotes() { return $this->gdoVar('dl_votes'); }
	
	public function getLevel() { return $this->gdoVar('dl_level'); }
	public function getPrice() { return $this->gdoVar('dl_price'); }
	public function displayPrice() { return GDT_Money::renderPrice($this->getPrice()); }
	public function getType() { return $this->getFile()->getType(); }
	public function getTitle() { return $this->gdoVar('dl_title'); }
	public function displayTitle() { return $this->gdoDisplay('dl_title'); }
	public function displayInfo() { return $this->gdoMessage()->renderCell(); }
	public function displayInfoText() { return $this->gdoMessage()->renderList(); }
	public function displaySize() { return $this->getFile()->displaySize(); }
	
	public function isAccepted() { return $this->gdoVar('dl_accepted') !== null; }
	public function isDeleted() : bool { return $this->gdoVar('dl_deleted') !== null; }
	public function isPaid() { return $this->getPrice() > 0; }

	##############
	### Render ###
	##############
	public function renderCard() : string
	{
		return GDT_Template::php('Download', 'card/download.php', ['gdo' => $this]);
	}
	
	public function renderList() : string
	{
		return GDT_Template::php('Download', 'list/download.php', ['download' => $this]);
	}
	
	#############
	### Cache ###
	#############
	public static function countDownloads()
	{
		if (false === ($cached = Cache::get('gdo_download_count')))
		{
			$cached = self::table()->countWhere("dl_deleted IS NULL AND dl_accepted IS NOT NULL");
			Cache::set('gdo_download_count', $cached);
		}
		return $cached;
	}
	
	public function gdoAfterCreate(GDO $gdo) : void
	{
		Cache::remove('gdo_download_count');
	}
	
	public function gdoAfterDelete()
	{
	    Cache::remove('gdo_download_count');
	}
	
}
