<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip;


/**
 * Language, page, block and other CMS content
 * Can be treated as a gateway to CMS content.
 *
 */
class Content
{
    /**
     * @var \Ip\Language[]
     */
    protected $languages;
    protected $blockContent;

    /**
     * @var \Ip\Language
     */
    protected $currentLanguage;

    /**
     * @var \Ip\Page
     */
    protected $currentPage;

    protected $currentRevision;

    public function __construct()
    {
    }

    /**
     * Get current language object
     * @return \Ip\Language
     */
    public function getCurrentLanguage()
    {
        return $this->currentLanguage;
    }

    /**
     * @ignore Used only for internal purposes
     */
    public function _setCurrentLanguage($currentLanguage)
    {
        $this->currentLanguage = $currentLanguage;
    }

    public function getPage($pageId)
    {
        try {
            $page = new \Ip\Page($pageId);
        } catch (\Ip\Exception $e) {
            return FALSE;
        }
        return $page;

    }

    /**
     * Get current page object
     *
     * @return \Ip\Page
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @ignore used only for internal purposes
     */
    public function _setCurrentPage($page)
    {
        $this->currentPage = $page;
        $this->currentRevision = null;
    }

    /**
     * Get specific language object
     * @param int $id Language ID
     * @return bool|Language
     */
    public function getLanguage($id)
    {
        $id = (int)$id;
        foreach ($this->getLanguages() as $language) {
            if ($language->getId() === $id) {
                return $language;
            }
        }
        return false;
    }

    /**
     * Get all website languages
     *
     * @return \Ip\Language[] All website languages. Each element is a Language object.
     *
     */
    public function getLanguages()
    {
        if ($this->languages === null) {
            $languages = \Ip\Internal\Languages\Service::getLanguages();
            $this->languages = array();
            foreach ($languages as $data) {
                $this->languages[] = \Ip\Internal\Content\Helper::createLanguage($data);
            }
        }
        return $this->languages;
    }

    /**
     * Get page block HTML content
     * @param string $block Block name
     * @return string HTML code
     */
    public function getBlockContent($block)
    {
        if (isset($this->blockContent[$block])) {
            return $this->blockContent[$block];
        } else {
            return null;
        }
    }

    /**
     * Set page block HTML content
     * @param string $block Block name
     * @param string $content HTML code
     */
    public function setBlockContent($block, $content)
    {
        $this->blockContent[$block] = $content;
    }

    /**
     * Generate block object
     *
     * @param string $blockName
     * @return Block
     */
    public function generateBlock($blockName)
    {
        return new \Ip\Block($blockName);
    }

    /**
     * If in management state and the last revision was published, create a new revision.
     * @ignore
     */
    public function getCurrentRevision()
    {
        if ($this->currentRevision !== null) {
            return $this->currentRevision;
        }

        if (!$this->currentPage) {
            return null;
        }

        $revision = null;
        $pageId = $this->currentPage->getId();

        if (ipIsManagementState()) {
            if (ipRequest()->getQuery('cms_revision')) {
                $revisionId = ipRequest()->getQuery('cms_revision');
                $revision = \Ip\Internal\Revision::getRevision($revisionId);
                if ($revision['pageId'] != $pageId) {
                    $revision = null;
                }
            }

            if (!$revision) {
                $revision = \Ip\Internal\Revision::getLastRevision($pageId);
                if ($revision['isPublished']) {
                    $duplicatedId = \Ip\Internal\Revision::duplicateRevision($revision['revisionId']);
                    $revision = \Ip\Internal\Revision::getRevision($duplicatedId);
                }
            }
        } else {
            $revision = \Ip\Internal\Revision::getPublishedRevision($this->currentPage->getId());
        }

        $this->currentRevision = $revision;
        return $this->currentRevision;
    }

    /**
     * Get a breadcrumb
     *
     * Gets an array of pages representing a tree path to a current page.
     *
     * @param int $pageId
     * @return \Ip\Page[]
     */
    public function getBreadcrumb($pageId = null)
    {
        if ($pageId !== null) {
            $page = new \Ip\Page($pageId);
        } else {
            $page = ipContent()->getCurrentPage();
        }

        if ($page) {
            $pages[] = $page;
            $parentPageId = $page->getParentId();
            while (!empty($parentPageId)) {
                $parentPage = new \Ip\Page($parentPageId);
                $pages[] = $parentPage;
                $parentPageId = $parentPage->getParentId();
            }
        }
        if (empty($pages)) {
            return array();
        }

        array_pop($pages);

        if (empty($pages)) {
            return array();
        }

        $breadcrumb = array_reverse($pages);

        return $breadcrumb;
    }

    /**
     * Get a page title
     *
     * @return string Title of the current page
     *
     */
    public function getTitle()
    {
        if ($this->currentPage) {
            return $this->currentPage->getTitle();
        }
    }

    /**
     * Get the current page description
     *
     * @return string Description of the current page
     *
     */
    public function getDescription()
    {
        if ($this->currentPage) {
            return $this->currentPage->getDescription();
        }
    }

    /**
     * Get the current page keywords
     *
     * @return string Keywords for the current page
     *
     */
    public function getKeywords()
    {
        if ($this->currentPage) {
            return $this->currentPage->getKeywords();
        }
    }

    /**
     * Add website language
     *
     * @param string $title
     * @param string $abbreviation
     * @param string $code
     * @param string $url
     * @param bool $visible
     * @param string $textDirection
     * @param null $position
     * @return mixed
     */
    public static function addLanguage($title, $abbreviation, $code, $url, $visible, $textDirection = 'ltr', $position = null)
    {
        $languageId = \Ip\Internal\Languages\Service::addLanguage($title, $abbreviation, $code, $url, $visible, $textDirection, $position = null);
        return $languageId;
    }

    /**
     * Delete a language with specific ID
     *
     * @param $languageId
     */
    public static function deleteLanguage($languageId)
    {
        \Ip\Internal\Languages\Service::delete($languageId);
    }







    /**
     * Update page data
     * @param int $pageId
     * @param array $data
     */
    public static function updatePage($pageId, $data)
    {
        \Ip\Internal\Pages\Service::updatePage($pageId, $data);
    }

    /**
     * Add a new page
     *
     * @param int $parentId Parent page ID
     * @param string $title
     * @param array $data
     * @return mixed
     */
    public static function addPage($parentId, $title, $data = array())
    {
        $newPageId = \Ip\Internal\Pages\Service::addPage($parentId, $title, $data );
        return $newPageId;
    }

    /**
     * Copy page
     *
     * @param int $pageId Source page ID
     * @param int $destinationParentId Target parent ID
     * @param int $destinationPosition
     * @return int New copied page ID
     */
    public static function copyPage($pageId, $destinationParentId, $destinationPosition)
    {
        $pageId = \Ip\Internal\Pages\Service::copyPage($pageId, $destinationParentId, $destinationPosition);
        return $pageId;
    }

    /**
     * Move a page to a different location on a website tree
     * @param int $pageId Source page ID
     * @param int $destinationParentId Target parent ID
     * @param int $destinationPosition
     */

    public static function movePage($pageId, $destinationParentId, $destinationPosition)
    {
        \Ip\Internal\Pages\Service::movePage($pageId, $destinationParentId, $destinationPosition);
    }

    /**
     * Delete a page
     * @param int $pageId
     */
    public static function deletePage($pageId)
    {
        \Ip\Internal\Pages\Service::deletePage($pageId);
    }


}
