<?php
namespace GDO\FFMpeg\Method;

use GDO\CLI\Process;
use GDO\Core\Method;
use GDO\FFMpeg\Module_FFMpeg;
use GDO\UI\TextStyle;

/**
 *
 * @author gizmore
 *
 */
final class AutodetectBinaries extends Method
{

	public function execute()
	{
		$this->detectBinariesIfNeeded();
	}

	public function detectBinariesIfNeeded(): void
	{
		$mod = Module_FFMpeg::instance();
		$path1 = $mod->cfgFFMpegPath();
		$path2 = $mod->cfgFFProbePath();
		if ((!$path1) || (!$path2))
		{
			$this->detectBinaries();
		}
	}

	public function detectBinaries(): void
	{
		$this->detectFFMpeg();
		$this->detectFFProbe();
	}

	public function detectFFMpeg(): void
	{
		if ($path = Process::commandPath('ffmpeg'))
		{
			$mod = Module_FFMpeg::instance();
			$mod->saveConfigVar('ffmpeg_path', $path);
			$this->message('msg_binary_detected', [TextStyle::bold('ffmpeg')]);
		}
		else
		{
			$this->error('err_file_not_found', ['ffmpeg']);
		}
	}

	public function detectFFProbe(): void
	{
		if ($path = Process::commandPath('ffprobe'))
		{
			$mod = Module_FFMpeg::instance();
			$mod->saveConfigVar('ffprobe_path', $path);
			$this->message('msg_binary_detected', [TextStyle::bold('ffprobe')]);
		}
		else
		{
			$this->error('err_file_not_found', ['ffprobe']);
		}
	}

}
