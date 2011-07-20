<?php
/**
 * An interface for objects that can be translated.
 *
 * @package    Elgg.Core
 * @subpackage SocialModel.Translatable
 */
interface Translatable {
	
	/**
	 * Array with ISO 639-1 language codes
	 */	
	/**
	 * Return languages array
	 */
	public function getLanguageCodes();

	/**
	 * Set language of a Translation
	 * @param string $language
	 */	
	public function setLanguage($language);

	/*
	 * Get language of a Translation
	 * @return string 
	 */
	public function getLanguage();

	/**
         * Add a translation to a blog
 	 * @param string $translation_guid
	 * @return bool	
         */
        public function addTranslation($translation_guid);

	/**
	 * Get a translation entity
	 * @param string $language
	 * @return Entity|false Depending on success
	 */
	public function getTranslation($language);

	/** 
	 * Delete a translation
	 * @param string $language
	 * @return Entity|false Depending on success
	 */
	public function deleteTranslation($language);

	/**
	 * Look if has some translations
	 * @return bool
	 */
	public function hasTranslations();
	
	/**
	 * See in the relations if is a translation of other blog
	 * @return bool
	 */
	public function isTranslation();

}
