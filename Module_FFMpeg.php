<?php
namespace GDO\FFMpeg;

use GDO\Core\GDO_Module;
use GDO\Core\WithComposer;
use GDO\Core\GDT_Path;
use GDO\FFMpeg\Method\AutodetectBinaries;
use FFMpeg\FFMpeg;
use GDO\Date\GDT_Duration;
use GDO\Date\Time;
use GDO\Core\GDT_UInt;
use GDO\CLI\Process;

/**
 * FFMPEG module.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 7.0.1
 */
final class Module_FFMpeg extends GDO_Module
{
	use WithComposer;
	
	##############
	### Module ###
	##############
	public int $priority = 45;
	public string $license = "LGPLv2.1";
	
	public function href_administrate_module() : ?string
	{
		return href('FFMpeg', 'AutodetectBinaries');
	}
	
	public function getLicenseFilenames() : array
	{
		return [
			'LICENSE.md',
			'ffmpeg/FFMPEG_LICENSE_INFO.md',
			'ffmpeg/LGPLv2.1.md',
			'vendor/php-ffmpeg/php-ffmpeg/LICENSE',
		];
	}
	
	public function getDependencies() : array
	{
		return [
			'CLI',
		];
	}
	
	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
			GDT_Path::make('ffmpeg_path')->existingFile(),
			GDT_Path::make('ffprobe_path')->existingFile(),
			GDT_Duration::make('ffmpeg_timeout')->notNull()->min(1)->max(Time::ONE_DAY)->initial('5m'),
			GDT_UInt::make('ffmpeg_threads')->min(1)->max(256)->initialValue(Process::cores()),
		];
	}
	public function cfgFFMpegPath() : ?string { return $this->getConfigVar('ffmpeg_path'); }
	public function cfgFFProbePath() : ?string { return $this->getConfigVar('ffprobe_path'); }
	public function cfgTimeout() : int { return (int) $this->getConfigValue('ffmpeg_timeout'); }
	public function cfgThreads() : int { return $this->getConfigValue('ffmpeg_threads'); }

	#############
	### Hooks ###
	#############
	public function onLoadLanguage() : void
	{
		$this->loadLanguage('lang/ffmpeg');
	}
	
	public function onInstall() : void
	{
		AutodetectBinaries::make()->detectBinariesIfNeeded();
	}
	
	##############
	### FFMPEG ###
	##############
	public function includeFFMpeg() : FFMpeg
	{
		$this->includeVendor();
		$config = [
			'ffmpeg.binaries' => $this->cfgFFMpegPath(),
			'ffprobe.binaries' => $this->cfgFFProbePath(),
			'timeout' => $this->cfgTimeout(),
			'ffmpeg.threads' => $this->cfgThreads(),
			'temporary_directory' => $this->tempPath(),
		];
		return FFMpeg::create($config);
	}
}
