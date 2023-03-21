<?php
namespace GDO\Download\Method;

use GDO\Core\GDO;
use GDO\Date\Time;
use GDO\Download\GDO_Download;
use GDO\Download\Module_Download;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodCrud;
use GDO\Language\Trans;
use GDO\Mail\Mail;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;

/**
 * Download CRUD form.
 * Sends approval mail.
 *
 * @version 7.0.1
 * @since 5.0.0
 *
 * @author gizmore
 * @see GDO_Download
 */
final class Crud extends MethodCrud
{

	public function gdoTable(): GDO { return GDO_Download::table(); }

	public function hrefList(): string { return href('Download', 'FileList'); }

	public function onRenderTabs(): void
	{
		Module_Download::instance()->renderTabs();
	}

	public function createForm(GDT_Form $form): void
	{
		$user = GDO_User::current();
		parent::createForm($form);
		if (!$user->hasPermission('staff'))
		{
			$form->removeField('dl_price');
		}
	}

	public function createFormButtons(GDT_Form $form): void
	{
		parent::createFormButtons($form);
		$user = GDO_User::current();
		if ($user->isStaff())
		{
			if (isset($this->gdo) && !$this->gdo->isAccepted())
			{
				$form->actions()->addField(GDT_Submit::make('accept'));
			}
		}
	}

	public function afterCreate(GDT_Form $form, GDO $gdo)
	{
		$user = GDO_User::current();
		if ($user->isStaff())
		{
			$gdo->saveVars([
				'dl_accepted' => Time::getDate(),
				'dl_acceptor' => GDO_User::system()->getID(),
			], false);
		}
		else
		{
			$this->onAcceptMail($form);
			return $this->message('msg_download_awaiting_accept');
		}
	}

	private function onAcceptMail(GDT_Form $form)
	{
		$iso = Trans::$ISO;
		foreach (GDO_User::admins() as $admin)
		{
			Trans::$ISO = $admin->getLangISO();
			$this->onAcceptMailTo($form, $admin);
		}
		Trans::$ISO = $iso;
	}

	###################
	### Accept Mail ###
	###################

	private function onAcceptMailTo(GDT_Form $form, GDO_User $user)
	{
		$dl = $this->gdo;
		$dl instanceof GDO_Download;

		# Sender
		$mail = Mail::botMail();

		# Body
		$username = $user->renderUserName();
		$sitename = sitename();
		$type = $dl->getType();
		$size = $dl->displaySize();
		$title = html($dl->getTitle());
		$info = $dl->displayInfo();
		$uploader = $dl->getCreator()->renderUserName();

		$link = GDT_Link::anchor(url('Download', 'Approve', "&id={$dl->getID()}&token={$dl->gdoHashcode()}"));
		$args = [$username, $sitename, $type, $size, $title, $info, $uploader, $link];
		$mail->setBody(tusr($user, 'mail_body_download_pending', $args));
		$mail->setSubject(tust($user, 'mail_subj_download_pending', [$sitename]));

		# Send
		$mail->sendToUser($user);
	}

	protected function crudCreateTitle()
	{
		$this->title(t('mt_download_upload', [sitename()]));
	}

}
