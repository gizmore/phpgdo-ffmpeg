<?php
namespace GDO\FFMpeg\Method;

use FFMpeg\Format\Audio\Mp3;
use GDO\CLI\MethodCLI;
use GDO\Core\GDT;
use GDO\Core\GDT_EnumNoI18n;
use GDO\Core\GDT_Path;
use GDO\Core\GDT_Response;
use GDO\FFMpeg\Module_FFMpeg;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;

final class ToMP3 extends MethodCLI
{

	public const AUDIO_PATTERN = '/\\.(?:m4a|opus)$/iD';

	public function isTrivial(): bool { return false; }

	public function getMethodTitle(): string
	{
		return t('mt_ffmpeg_alltomp3');
	}

	public function getMethodDescription(): string
	{
		return t('md_ffmpeg_alltomp3');
	}

	protected function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_EnumNoI18n::make('bitrate')->enumValues('128kb/s', '192kb/s', '256kb/s', 'Dynamic kb/s')->initial('192kb/s'),
			GDT_Path::make('file')->existingFile()->notNull()->pattern(self::AUDIO_PATTERN),
		);
		$form->actions()->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form): GDT
	{
		$mod = Module_FFMpeg::instance();
		$path = $this->getPath();
		$out = "$path.mp3";
		$ffmpeg = $mod->includeFFMpeg();
		$audio = $ffmpeg->open($path);
		$format = new Mp3();
		$format->setAudioKiloBitrate($this->getBitrate());
		$audio->save($format, $out);
		$this->message('msg_ffmpeg_to_mp3', [html(basename($out))]);
		return GDT_Response::make();
	}

	public function getPath(): string
	{
		return $this->gdoParameterVar('file');
	}

	public function getBitrate(): int
	{
		$enum = $this->gdoParameterVar('bitrate');
		$bits = substr($enum, 0, 3);
		return is_numeric($bits) ? intval($bits) : 0;
	}

}
