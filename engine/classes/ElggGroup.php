<?php

/**
 * Class representing a container for other elgg entities.
 *
 * @package    Elgg.Core
 * @subpackage Groups
 */
class ElggGroup extends ElggEntity
	implements Friendable, Translatable {
	
	public $languages = array(
		"aa", "ab", "af", "am", "ar", "as", "ay", "az", "ba", "be", "bg", "bh", "bi", "bn", "bo", "br",	"ca", "co", "cs", "cy",	"da",
		"de", "dz", "el", "en", "eo", "es", "et", "eu", "fa", "fi", "fj", "fo", "fr", "fy", "ga", "gd", "gl", "gn", "gu", "he", "ha",
		"hi", "hr", "hu", "hy", "ia", "id", "ie", "ik",	/*"i,*/"is", "it", "iu", "iw", "ja", "ji", "jw", "ka", "kk", "kl", "km", "kn",
		"ko", "ks", "ku", "ky", "la", "ln", "lo", "lt", "lv", "mg", "mi", "mk", "ml", "mn", "mo", "mr", "ms", "mt", "my", "na", "ne",
		"nl", "no", "oc", "om", "or", "pa", "pl", "ps", "pt", "qu", "rm", "rn", "ro", "ru", "rw", "sa", "sd", "sg", "sh", "si", "sk",
		"sl", "sm", "sn", "so", "sq", "sr", "ss", "st", "su", "sv", "sw", "ta", "te", "tg", "th", "ti", "tk", "tl", "tn", "to", "tr",
		"ts", "tt", "tw", "ug", "uk", "ur", "uz", "vi", "vo", "wo", "xh",/*"y,*/"yi", "yo", "za", "zh",	"zu",

	);

	
	/**
	 * Sets the type to group.
	 *
	 * @return void
	 *
	 * @deprecated 1.8 Use initializeAttributes
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['type'] = "group";
		$this->attributes['name'] = NULL;
		$this->attributes['description'] = NULL;
		$this->attributes['tables_split'] = 2;
	}

	/**
	 * Construct a new user entity, optionally from a given id value.
	 *
	 * @param mixed $guid If an int, load that GUID.
	 * 	If a db row then will attempt to load the rest of the data.
	 *
	 * @throws Exception if there was a problem creating the user.
	 */
	function __construct($guid = null) {
		$this->initializeAttributes();

		// compatibility for 1.7 api.
		$this->initialise_attributes(false);

		if (!empty($guid)) {
			// Is $guid is a DB row - either a entity row, or a user table row.
			if ($guid instanceof stdClass) {
				// Load the rest
				if (!$this->load($guid->guid)) {
					$msg = elgg_echo('IOException:FailedToLoadGUID', array(get_class(), $guid->guid));
					throw new IOException($msg);
				}

				// Is $guid is an ElggGroup? Use a copy constructor
			} else if ($guid instanceof ElggGroup) {
				elgg_deprecated_notice('This type of usage of the ElggGroup constructor was deprecated. Please use the clone method.', 1.7);

				foreach ($guid->attributes as $key => $value) {
					$this->attributes[$key] = $value;
				}

				// Is this is an ElggEntity but not an ElggGroup = ERROR!
			} else if ($guid instanceof ElggEntity) {
				throw new InvalidParameterException(elgg_echo('InvalidParameterException:NonElggGroup'));

				// We assume if we have got this far, $guid is an int
			} else if (is_numeric($guid)) {
				if (!$this->load($guid)) {
					throw new IOException(elgg_echo('IOException:FailedToLoadGUID', array(get_class(), $guid)));
				}
			} else {
				throw new InvalidParameterException(elgg_echo('InvalidParameterException:UnrecognisedValue'));
			}
		}
	}

	/**
	 * Add an ElggObject to this group.
	 *
	 * @param ElggObject $object The object.
	 *
	 * @return bool
	 */
	public function addObjectToGroup(ElggObject $object) {
		return add_object_to_group($this->getGUID(), $object->getGUID());
	}

	/**
	 * Remove an object from the containing group.
	 *
	 * @param int $guid The guid of the object.
	 *
	 * @return bool
	 */
	public function removeObjectFromGroup($guid) {
		return remove_object_from_group($this->getGUID(), $guid);
	}

	/**
	 * Returns an attribute or metadata.
	 *
	 * @see ElggEntity::get()
	 *
	 * @param string $name Name
	 *
	 * @return mixed
	 */
	public function get($name) {
		if ($name == 'username') {
			return 'group:' . $this->getGUID();
		}
		return parent::get($name);
	}
	
	/**
	 * Start translatable compatibility block:
	 * 
	 *	public function languageCodes();
		public function setLanguage($language);
		public function getLanguage();
		public function addTranslation($translation_guid);
		public function getTrasnlation($language);
		public function deleteTranslation($language);
		public function hasTranslations();
		public function isTranslation();
	*/
	
	/**
	 * Return languages array
	 */
	public function getLanguageCodes(){
		return $languages;
	}

	/**
	 * Set language of a Translation
	 * @param string $language
	 */	
	public function setLanguage($language){
		$this->language = $language;
	}

	/*
	 * Get language of a Translation
	 * @return string 
	 */
	public function getLanguage(){
		return $this->language;
	}

	/**
         * Add a translation to a group
 	 * @param string $translation_guid
	 * @return bool	
         */
        public function addTranslation($translation_guid){
		$group_guid = $this->getGUID();
		error_log("ADDING TRANSLATION TO " . $this->guid . " WITH " . $translation_guid); 
		
	        if ($group_guid == $translation_guid) {
                        return false;
                }
                if (!$translation = get_entity($translation_guid)) {
                        return false;
                }
                if (!$group = get_entity($blog_guid)) {
                        return false;
                }
                if ((!($group instanceof ElggGroup)) || (!($translation instanceof ElggGroup))) {
                        return false;
                }
                return add_entity_relationship($group_guid, "translation", $translation_guid);

	}

	/**
	 * Get a translation entity of this group
	 * @param string $language
	 * @return Entity|false Depending on success
	 */
	public function getTranslation($language){	
		$entities = elgg_get_entities_from_relationship(array(
			'relationship' => "translation",
			'relationship_guid' => $this->getGUID()
        	));
		foreach($entities as $entity){
			if($entity->language == $language){
				return $entity;
			}else{
				return false;
			}
		}
	}

	/** 
	 * Delete a translation
	 * @param string $language
	 * @return Entity|false Depending on success
	 */
	public function deleteTranslation($language){
		if($entity = getTranslation($language)){
			//Change object for group??
			if(elgg_instanceof($entity,'object','translation')){
				$entity->delete();
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * Look if has some translations
	 * @return bool
	 */
	public function hasTranslations(){
		$translations= FALSE;
		$relationships = get_entity_relationships($this->getGUID());
		
		foreach($relationships as $relation){
			if($relation->relationship == 'translation'){
				$translations = TRUE;
			}
		}
		if($translations){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	/**
	 * See in the relations if is a translation of other blog
	 * @return bool
	 */
	public function isTranslation(){	
		$translation = FALSE;
		$relationships = get_entity_relationships($this->getGUID(), TRUE);
		foreach($relationships as $relation){
			if($relation->relationship == 'translation' && $relation->guid_two == $this->getGUID()){
				$translation = TRUE;
			}
		}
		if($translation){
			return TRUE;
		}else{
			return FALSE;
		}
	}

			

	/**
	 * Start friendable compatibility block:
	 *
	 * 	public function addFriend($friend_guid);
		public function removeFriend($friend_guid);
		public function isFriend();
		public function isFriendsWith($user_guid);
		public function isFriendOf($user_guid);
		public function getFriends($subtype = "", $limit = 10, $offset = 0);
		public function getFriendsOf($subtype = "", $limit = 10, $offset = 0);
		public function getObjects($subtype="", $limit = 10, $offset = 0);
		public function getFriendsObjects($subtype = "", $limit = 10, $offset = 0);
		public function countObjects($subtype = "");
	 */

	/**
	 * For compatibility with Friendable.
	 *
	 * Join a group when you friend ElggGroup.
	 *
	 * @param int $friend_guid The GUID of the user joining the group.
	 *
	 * @return bool
	 */
	public function addFriend($friend_guid) {
		return $this->join(get_entity($friend_guid));
	}

	/**
	 * For compatibility with Friendable
	 *
	 * Leave group when you unfriend ElggGroup.
	 *
	 * @param int $friend_guid The GUID of the user leaving.
	 *
	 * @return bool
	 */
	public function removeFriend($friend_guid) {
		return $this->leave(get_entity($friend_guid));
	}

	/**
	 * For compatibility with Friendable
	 *
	 * Friending a group adds you as a member
	 *
	 * @return bool
	 */
	public function isFriend() {
		return $this->isMember();
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param int $user_guid The GUID of a user to check.
	 *
	 * @return bool
	 */
	public function isFriendsWith($user_guid) {
		return $this->isMember($user_guid);
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param int $user_guid The GUID of a user to check.
	 *
	 * @return bool
	 */
	public function isFriendOf($user_guid) {
		return $this->isMember($user_guid);
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param string $subtype The GUID of a user to check.
	 * @param int    $limit   Limit
	 * @param int    $offset  Offset
	 *
	 * @return bool
	 */
	public function getFriends($subtype = "", $limit = 10, $offset = 0) {
		return get_group_members($this->getGUID(), $limit, $offset);
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param string $subtype The GUID of a user to check.
	 * @param int    $limit   Limit
	 * @param int    $offset  Offset
	 *
	 * @return bool
	 */
	public function getFriendsOf($subtype = "", $limit = 10, $offset = 0) {
		return get_group_members($this->getGUID(), $limit, $offset);
	}

	/**
	 * Get objects contained in this group.
	 *
	 * @param string $subtype Entity subtype
	 * @param int    $limit   Limit
	 * @param int    $offset  Offset
	 *
	 * @return array|false
	 */
	public function getObjects($subtype = "", $limit = 10, $offset = 0) {
		return get_objects_in_group($this->getGUID(), $subtype, 0, 0, "", $limit, $offset, false);
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param string $subtype Entity subtype
	 * @param int    $limit   Limit
	 * @param int    $offset  Offset
	 *
	 * @return array|false
	 */
	public function getFriendsObjects($subtype = "", $limit = 10, $offset = 0) {
		return get_objects_in_group($this->getGUID(), $subtype, 0, 0, "", $limit, $offset, false);
	}

	/**
	 * For compatibility with Friendable
	 *
	 * @param string $subtype Subtype of entities
	 *
	 * @return array|false
	 */
	public function countObjects($subtype = "") {
		return get_objects_in_group($this->getGUID(), $subtype, 0, 0, "", 10, 0, true);
	}

	/**
	 * End friendable compatibility block
	 */

	/**
	 * Get a list of group members.
	 *
	 * @param int  $limit  Limit
	 * @param int  $offset Offset
	 * @param bool $count  Count
	 *
	 * @return mixed
	 */
	public function getMembers($limit = 10, $offset = 0, $count = false) {
		return get_group_members($this->getGUID(), $limit, $offset, 0, $count);
	}

	/**
	 * Returns whether the current group is public membership or not.
	 *
	 * @return bool
	 */
	public function isPublicMembership() {
		if ($this->membership == ACCESS_PUBLIC) {
			return true;
		}

		return false;
	}

	/**
	 * Return whether a given user is a member of this group or not.
	 *
	 * @param ElggUser $user The user
	 *
	 * @return bool
	 */
	public function isMember($user = 0) {
		if (!($user instanceof ElggUser)) {
			$user = elgg_get_logged_in_user_entity();
		}
		if (!($user instanceof ElggUser)) {
			return false;
		}
		return is_group_member($this->getGUID(), $user->getGUID());
	}

	/**
	 * Join an elgg user to this group.
	 *
	 * @param ElggUser $user User
	 *
	 * @return bool
	 */
	public function join(ElggUser $user) {
		return join_group($this->getGUID(), $user->getGUID());
	}

	/**
	 * Remove a user from the group.
	 *
	 * @param ElggUser $user User
	 *
	 * @return void
	 */
	public function leave(ElggUser $user) {
		return leave_group($this->getGUID(), $user->getGUID());
	}

	/**
	 * Override the load function.
	 * This function will ensure that all data is loaded (were possible), so
	 * if only part of the ElggGroup is loaded, it'll load the rest.
	 *
	 * @param int $guid GUID of an ElggGroup entity
	 *
	 * @return true
	 */
	protected function load($guid) {
		// Test to see if we have the generic stuff
		if (!parent::load($guid)) {
			return false;
		}

		// Check the type
		if ($this->attributes['type'] != 'group') {
			$msg = elgg_echo('InvalidClassException:NotValidElggStar', array($guid, get_class()));
			throw new InvalidClassException($msg);
		}

		// Load missing data
		$row = get_group_entity_as_row($guid);
		if (($row) && (!$this->isFullyLoaded())) {
			// If $row isn't a cached copy then increment the counter
			$this->attributes['tables_loaded'] ++;
		}

		// Now put these into the attributes array as core values
		$objarray = (array) $row;
		foreach ($objarray as $key => $value) {
			$this->attributes[$key] = $value;
		}

		return true;
	}

	/**
	 * Override the save function.
	 *
	 * @return bool
	 */
	public function save() {
		// Save generic stuff
		if (!parent::save()) {
			return false;
		}

		// Now save specific stuff
		return create_group_entity($this->get('guid'), $this->get('name'), $this->get('description'));
	}

	// EXPORTABLE INTERFACE ////////////////////////////////////////////////////////////

	/**
	 * Return an array of fields which can be exported.
	 *
	 * @return array
	 */
	public function getExportableValues() {
		return array_merge(parent::getExportableValues(), array(
			'name',
			'description',
		));
	}

	/**
	 * Can a user comment on this group?
	 *
	 * @see ElggEntity::canComment()
	 *
	 * @param int $user_guid User guid (default is logged in user)
	 * @return bool
	 * @since 1.8.0
	 */
	public function canComment($user_guid = 0) {
		$result = parent::canComment($user_guid);
		if ($result !== null) {
			return $result;
		}
		return false;
	}
}
