<?php

namespace Trek;

interface VersionInterface
{
		const INITIAL = '0.0.0';

    const REGEX = '/(\d+)\.?(\d*)\.?(\d*)-?(alpha|beta|rc)?\.?(\d*)/';

		public function __toString();

		public function compare($version);

		public function ns();
}