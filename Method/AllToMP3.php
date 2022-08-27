<?php
namespace GDO\FFMpeg\Method;

use GDO\CLI\MethodCLI;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Core\GDT_Path;
use GDO\Util\Filewalker;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_EnumNoI18n;

/**
 * Convert all files in a folder to mp3.
 * 
 * @author gizmore
 * @since 7.0.1
 */
final class AllToMP3 extends MethodCLI
{
	public function isTrivial() : bool { return false; }
	
	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_Checkbox::make('recursive')->notNull()->initial('0'),
			GDT_EnumNoI18n::make('bitrate')->enumValues('128kb/s', '192kb/s', '256kb/s', 'Dynamic kb/s')->initial('192kb/s'),
			GDT_Path::make('path')->existingDir()->notNull(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}
	
	public function getPath() : string
	{
		return $this->gdoParameterVar('path');
	}
	
	public function formValidated(GDT_Form $form): void
	{
		$path = $this->getPath();
		$ptrn = ToMP3::AUDIO_PATTERN;
		$cbck = [$this, 'callbackConvert'];
		Filewalker::traverse($path, $ptrn, $cbck, null, 0);
	}
	
	public function callbackConvert(string $entry, string $fullpath, $args=null) : void
	{
		$input = [
			'bitrate' => $this->gdoParameterVar('bitrate'),
			'file' => $fullpath,
			'submit' => 1,
		];
		ToMP3::make()->executeWithInputs($input);
	}
	
}
