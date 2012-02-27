<?php

namespace Trek;

interface VersionInterface
{
	public function __toString();
	
	public function compare($version);
	
	public function ns();
}