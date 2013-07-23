<?php

namespace Trek;

interface VersionInterface
{
		const INITIAL = '0.0.0';

    const REGEX = '/(\d+)\.?(\d*)\.?(\d*)-?(dev|alpha|a|beta|b|rc|pl|p)?\.?(\d*)/';

		public function __toString();

		public function compare($version);

		public function ns();
}