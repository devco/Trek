<?php

namespace Trek;

interface VersionInterface
{
		const INITIAL = '0.0.0';

    const REGEX = '/(\d+)\.?(\d*)\.?(\d*)-?(dev|a|alpha|b|beta|rc|p|pl)?\.?(\d*)/';

		public function __toString();

		public function compare($version);

		public function ns();
}