<?php
/**
 * @author Björn Schießle <schiessle@owncloud.com>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2016, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */
namespace OCA\DAV\Command;

use OCA\DAV\CardDAV\CardDavBackend;
use OCA\DAV\Connector\Sabre\Principal;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\ILogger;
use OCP\IUserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAddressBook extends Command {

	/** @var IUserManager */
	protected $userManager;

	/** @var \OCP\IDBConnection */
	protected $dbConnection;

	/** @var ILogger  */
	private $logger;

	/** @var IGroupManager $groupManager */
	private $groupManager;

	/**
	 * @param IUserManager $userManager
	 * @param IDBConnection $dbConnection
	 * @param IConfig $config
	 * @param ILogger $logger
	 */
	function __construct(IUserManager $userManager,
						 IGroupManager $groupManager,
						 IDBConnection $dbConnection,
						 ILogger $logger
	) {
		parent::__construct();
		$this->userManager = $userManager;
		$this->groupManager = $groupManager;
		$this->dbConnection = $dbConnection;
		$this->logger = $logger;
	}

	protected function configure() {
		$this
				->setName('dav:create-addressbook')
				->setDescription('Create a dav addressbook')
				->addArgument('user',
						InputArgument::REQUIRED,
						'User for whom the addressbook will be created')
				->addArgument('name',
						InputArgument::REQUIRED,
						'Name of the addressbook');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$user = $input->getArgument('user');
		if (!$this->userManager->userExists($user)) {
			throw new \InvalidArgumentException("User <$user> in unknown.");
		}
		$principalBackend = new Principal(
				$this->userManager,
				$this->groupManager
		);

		$name = $input->getArgument('name');
		$carddav = new CardDavBackend($this->dbConnection, $principalBackend, $this->logger);
		$carddav->createAddressBook("principals/users/$user", $name, []);
	}
}