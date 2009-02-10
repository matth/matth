<?php 

	if ($argc == 1) {
		
		$extendedUsage = <<<END
This simple script will unpack tar.gz files. It will store a record of the last
unpacked version and only unpack higher subsequesnt versions, based on the below
naming conventions. After files are unpacked the tar is deleted.

Naming convention:

	packagename-0.1.tar.gz
	packagename-0.1.1.tar.gz

In the above instance the tar called 'packagename-0.1.1.tar.gz' will be unpacked 
over 'packagename-0.1.tar.gz'

Author: mhaynes 2009
Version: 0.1


END;
		fwrite(STDOUT, 'usage: ' . $argv[0] . " package_directory \n\n");
		fwrite(STDOUT, $extendedUsage); 
		
		exit(1);
		
	} else {
		
		$packageDirectory = $argv[1];
		
		if (!is_dir($packageDirectory)) {
			fwrite(STDERR, "ERROR: Cannot find specified directory \n");
			exit(1);
		}
		
		$d = dir($packageDirectory);
		
		while (false !== ($entry = $d->read())) {
			
			if (preg_match('/tar.gz$/', $entry) == 1) {

				// Get version number
				preg_match('/(.*)-(.*).tar.gz/', $entry, $matches);
				
				$packageName = $packageDirectory . '/' . $matches[1];
				$versionNumber = $matches[2];
				
				// Check if a preivously installed version exists
				$unpack = true;
				
				if (file_exists($packageName)) {
					// Is version number higher?
					if (version_compare($versionNumber, file_get_contents($packageName)) != 1) {
						$unpack = false;
						fwrite(STDERR, "ERROR: Package $entry is older than latest deployed version \n");
					}
				} 
				
				if ($unpack) {
					
					fwrite(STDERR, "Unpacking $entry \n");
					
					// Update file
					$fp = fopen($packageName, 'w');
					fwrite($fp, $versionNumber);
					fclose($fp);
					
					// Untar
					system('/bin/tar -xzf ' . $packageDirectory . '/' . $entry . ' -C / ');

					// Delete tar
					unlink( $packageDirectory . '/' . $entry);	
					
					fwrite(STDERR, "Package $entry succesfully deployed \n");
					
				}
				
			}
		}		
		
		$d->close();
		
		
		exit(0);
	}
	
?>