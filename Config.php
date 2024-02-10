<?php

namespace RI\CreditCalc;

use \Bitrix\Main\Localization\Loc;


if (!class_exists(__NAMESPACE__ . "\\Config")) {

	/**
	* Class Config
	*/
	class Config {
		/**
		* Module ID
		*/
		private $id;

		/**
		* Module name
		*/
		private $name;

		/**
		* Module description
		*/
		private $description;

		/**
		* Module partner name
		*/
		private $partnerName;

		/**
		* Module partner url
		*/
		private $partnerUri;

		/**
		* Module site URL
		*/
		private $siteURL;

		private static $instance = null;

		/**
		* Function construct
		*/
		private function __construct() {

			Loc::loadMessages(__FILE__);

			$this->id = "ri.creditcalc";
			$this->name = "Форма обратной связи \"Кредитный калькулятор\"";
			$this->description = "Форма заявки на кредит по ИНН и номеру телефона";
			$this->partnerName = "Panfilov";
			$this->partnerUri = "https://panfilov.org";
			$this->siteURL = sprintf("http%s://%s", isset($_SERVER["HTTPS"]) ? "s" : "", $_SERVER["SERVER_NAME"]);
		}

		/**
		* function getInstance()
		* @return object
		*/
		public static function getInstance() {
			if (self::$instance === null) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		* @return string
		*/
		public function getId(): string {
			return $this->id;
		}

		/**
		* @return string
		*/
		public function getName(): string {
			return $this->name;
		}

		/**
		* @return string
		*/
		public function getDescription(): string {
			return $this->description;
		}

		/**
		* @return string
		*/
		public function getPartnerName(): string {
			return $this->partnerName;
		}

		/**
		* @return string
		*/
		public function getPartnerUri(): string {
			return $this->partnerUri;
		}


		/**
		* @return string
		*/
		public function getSiteURL(): string {
			return $this->siteURL;
		}

		/**
		 * @return string
		 */
		public function getDataPath(): string {
			return __DIR__ . '/data';
		}
	}
}
