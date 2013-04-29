<?php


	class CreateRepo {
		var $MAVEN_METADATA_XML = "maven-metadata.xml";

		var $PLOVR_PATH = "https://plovr.googlecode.com/files/plovr-%VERSION%.jar";

		var $xmlPath = "org/plovr/plovr/maven-metadata.xml";

		var $GROUP_ID = "org.plovr";
		var $ARTIFACT_ID = "plovr";

		var $latestVersionDate = "20121028000000";

		var $versions = array(
			"eba786b34df9",
			"4b3caf2b7d84",
			"d6db24beeb7f",
			"96feca4d303b",
			"c047fb78efb8",
			"0744c5209a34",
			"cf41182d522c+",
			"0751912cc154",
			"35c0328e9fbf+",
			"63428929c594",
			"f3c3754b1f2a",
			"09488834dad1",
			"5c0a7bcad95a",
			"16d1d062a102+",
			"c0109c239c9a",
			"9889af94e3d3",
			"6b3b4e9f58a6"
		);


		public function __construct() {
			$this->createFolderStructure();
			$this->createXml();
			$this->getAllJars();
			$this->writeMD5();
			$this->createPOMs();
		}

		public function createXml() {
			$xml = new SimpleXMLElement( '<metadata />' );

			// artifact and group id
			$xml->addChild( "groupId", $this->GROUP_ID );
			$xml->addChild( "artifactId", $this->ARTIFACT_ID );

			$versioning = $xml->addChild( "versioning" );
			$versioning->addChild( "release", $this->versions[ 0 ] );
			$versioning->addChild( "lastUpdated", $this->latestVersionDate );

			$versions = $versioning->addChild( "versions" );

			foreach( $this->versions as $version ) {
				$versions->addChild( "version", $version );
			}

			$xml = $xml->asXML();

			$xmlPath = $this->getRepoRoot() . "/" . $this->MAVEN_METADATA_XML;
			$this->saveFile( $xmlPath, $xml );

			$this->saveHashes( $xmlPath );
		}

		public function createPOMs() {
			foreach( $this->versions as $version ) {
				$xml = new SimpleXMLElement( "<project />" );

				$xml->addChild( "modelVersion", "4.0.0" );
				$xml->addChild( "groupId", $this->GROUP_ID );
				$xml->addChild( "artifactId", $this->ARTIFACT_ID );
				$xml->addChild( "version", $version );

				$pomPath = $this->getPomPath( $version );

				$this->saveFile( $pomPath, $xml->asXML() );
				$this->saveHashes( $pomPath );
			}
		}

		private function createFolderStructure() {
			$this->removeFolder( "org" );

			$rootPath = $this->getRepoRoot();

			foreach( $this->versions as $version ) {
				mkdir( $rootPath . "/" . $version, 0755, true );
			}
		}

		private function removeFolder( $dir ) {
			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, RecursiveDirectoryIterator::SKIP_DOTS ), RecursiveIteratorIterator::CHILD_FIRST );

			foreach( $files as $fileinfo ) {
				$todo = ( $fileinfo->isDir() ? 'rmdir' : 'unlink' );
				$todo( $fileinfo->getRealPath() );
			}
		}

		private function saveFile( $path, $contents ) {
			file_put_contents( $path, $contents );
		}

		private function getRepoRoot() {
			return str_replace( ".", "/", $this->GROUP_ID ) . "/" . $this->ARTIFACT_ID;
		}

		private function getRemoteFile( $src, $dest ) {
			print "Saving $src ... ";
			file_put_contents( $dest, fopen( $src, 'r' ) );
			print " DONE\n";
		}

		private function getAllJars() {
			foreach( $this->versions as $version ) {
				$downloadUrl = $this->getDownloadUrl( $version );
				$destPath = $this->getLocalFilePath( $version );

				$this->getRemoteFile( $downloadUrl, $destPath );
			}
		}

		private function getLocalFilePath( $version ) {
			return $this->getRepoRoot() . "/" . $version . "/" . $this->getFileName( $version );
		}

		private function getFileName( $version ) {
			return basename( $this->getDownloadUrl( $version ) );
		}

		private function getDownloadUrl( $version ) {
			return str_replace( "%VERSION%", $version, $this->PLOVR_PATH );
		}

		private function saveHashes( $path ) {
			$this->saveMD5( $path );
			$this->saveSHA1( $path );
		}

		private function writeMD5() {
			foreach( $this->versions as $version ) {
				$localPath = $this->getLocalFilePath( $version );
				$this->saveHashes( $localPath );
			}
		}

		private function saveMD5( $localPath ) {
			$destPath = $localPath . ".md5";
			$md5 = md5_file( $localPath );

			$this->saveFile( $destPath, $md5 );
		}


		private function saveSHA1( $localPath ) {
			$destPath = $localPath . ".sha1";
			$md5 = sha1_file( $localPath );

			$this->saveFile( $destPath, $md5 );
		}

		private function getPomPath( $version ) {
			return str_replace( ".jar", ".pom", $this->getLocalFilePath( $version ) );
		}
	}

	new CreateRepo();
