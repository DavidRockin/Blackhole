<?php

namespace App;
class Pagination
{

	/**
	 * The amount of results
	 */
	private $totalResults   = 0;

	/**
	 * The amount of results per page
	 */
	private $resultsPerPage = 0;

	/**
	 * Current page
	 */
	private $currentPage    = 0;

	/**
	 * Amount of stages for rendering
	 */
	private $stages         = 3;

	/**
	 * Url for pages
	 */
	private $url            = "?page=%d";

	/**
	 * The previous page
	 */
	private $previousPage   = 0;

	/**
	 * Next page number
	 */
	private $nextPage       = 0;

	/**
	 * Last page number
	 *
	 * This property also represents the amount of pages
	 */
	private $lastPage       = 0;

	/**
	 * Pagination template
	 */
	private $template       = array(
	                              // pagination container
	                              "wrapper"    => "<nav>%s</nav>",
	                              "container"  => "<ul class=\"pagination\">%s</ul>",

	                              // pagination link
	                              "page"       => "<li class=\"%s\">%s</li>",
	                              "page_empty" => "<li>%s</li>",
	                              "link"       => "<a href=\"%s\">%s</a>",
	                              "link_empty" => "<a>%s</a>",

	                              // labels
	                              "next"       => "Next &raquo;",
	                              "previous"   => "&laquo; Previous",
	                          );

	/**
	 * Processes the given information, and returns the HTML
	 */
	public function render()
	{
		// process given information
		$this->process();

		// build pagination
		$html = "";

		// display the previous page?
		if ($this->currentPage > 1) {
			$html .= $this->writePage(
				$this->previousPage,
				null,
				$this->template['previous']
			);
		} else {
			$html .= $this->writeLabel(
				$this->template['previous'],
				"disabled"
			);
		}

		// build links
		if ($this->lastPage < 7 + ($this->stages*2)) {
			// write the pages
			for ($pages = 1; $pages <= $this->lastPage; ++$pages) {
				$html .= $this->writePage(
					$pages,
					($pages == $this->currentPage ? "active": "")
				);
			}
		} else if ($this->lastPage > 5 + ($this->stages*2)) {
			if ($this->currentPage < 1+($this->stages*2)) {
				// write the pages
				for ($pages = 1; $pages < 2+($this->stages*2); ++$pages) {
					$html .= $this->writePage(
						$pages,
						($pages == $this->currentPage ? "active": "")
					);
				}

				// write last 2 pages
				$html .= $this->writeLabel("..", "disabled");
				$html .= $this->writePage($this->lastPage-1);
				$html .= $this->writePage($this->lastPage);
			} else if ($this->lastPage - ($this->stages*2) > $this->currentPage && $this->currentPage > $this->stages*2) {
				// write first 2 pages
				$html .= $this->writePage(1);
				$html .= $this->writePage(2);
				$html .= $this->writeLabel("..", "disabled");

				// write the pages
				for ($pages = ($this->currentPage-$this->stages); $pages < ($this->currentPage+$this->stages)+1; ++$pages) {
					$html .= $this->writePage(
						$pages,
						($pages == $this->currentPage ? "active": "")
					);
				}

				// write the last 2 pages
				$html .= $this->writeLabel("..", "disabled");
				$html .= $this->writePage($this->lastPage-1);
				$html .= $this->writePage($this->lastPage);
			} else {
				// write the first 2 pages
				$html .= $this->writePage(1);
				$html .= $this->writePage(2);
				$html .= $this->writeLabel("..", "disabled");

				// write the pages
				for ($pages = ($this->lastPage-($this->stages*2)); $pages <= $this->lastPage; ++$pages) {
					$html .= $this->writePage(
						$pages,
						($pages == $this->currentPage ? "active": "")
					);
				}
			}
		}

		// display next button
		if ($this->currentPage < $pages-1) {
			$html .= $this->writePage(
				$this->nextPage,
				null,
				$this->template['next']
			);
		} else {
			$html .= $this->writeLabel(
				$this->template['next'],
				"disabled"
			);
		}

		// final processing
		$html = sprintf($this->template['container'], $html);
		$html = sprintf($this->template['wrapper'],   $html);
		return $html;
	}

	/**
	 * Process given information
	 */
	private function process()
	{
		$this->previousPage = $this->currentPage-1;
		$this->nextPage     = $this->currentPage+1;
		$this->lastPage     = ceil($this->totalResults / $this->resultsPerPage);

		// prevent broken pages, if current page > last page
		if ($this->currentPage > $this->lastPage) {
			$this->currentPage  = $this->lastPage;
			$this->previousPage = $this->lastPage-1;
		}

		return $this;
	}

	/**
	 * Sets the total results
	 */
	public function setTotalResults($results)
	{
		$this->totalResults = $results;
		return $this;
	}

	/**
	 * Sets the amount of results per page
	 */
	public function setResultsPerPage($results)
	{
		$this->resultsPerPage = $results;
		return $this;
	}

	/**
	 * Sets the current page
	 */
	public function setCurrentPage($page)
	{
		$this->currentPage = $page;
		return $this;
	}

	/**
	 * Sets the pagination stages
	 */
	public function setStages($stages)
	{
		$this->stages = $stages;
		return $this;
	}

	/**
	 * Sets the page URL
	 */
	public function setURL($url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * Sets a template option
	 */
	public function setTemplateOption($key, $value)
	{
		if (isset($this->template[$key]))
			$this->template[$key] = $value;
		return $this;
	}

	/**
	 * Returns the total results
	 */
	public function getTotalResults()
	{
		return $this->totalResults;
	}

	/**
	 * Returns the total results per page
	 */
	public function getResultsPerPage()
	{
		return $this->resultsPerPage;
	}

	/**
	 * Returns the previous page number
	 */
	public function getPreviousPage()
	{
		return $this->previousPage;
	}

	/**
	 * Returns the next page number
	 */
	public function getNextPage()
	{
		return $this->nextPage;
	}

	/**
	 * Returns the last page number
	 *
	 * This is also used to determine the amount of pages there are
	 */
	public function getLastPage()
	{
		return $this->lastPage;
	}

	/**
	 * Returns the MySQL start number
	 */
	public function getMySQLStart()
	{
		return ($this->currentPage-1)*$this->resultsPerPage;
	}

	/**
	 * Returns the current page number
	 */
	public function getCurrentPage()
	{
		return $this->currentPage;
	}

	/**
	 * Returns the current stage number
	 */
	public function getStages()
	{
		return $this->stages;
	}

	/**
	 * Returns the page URL
	 */
	public function getURL()
	{
		return $this->url;
	}

	/**
	 * Returns a template option
	 */
	public function getTemplateOption($key)
	{
		return $this->template[$key];
	}

	/**
	 * Processes the page URL with a number
	 */
	private function getPageURL($page)
	{
		return str_replace("%p", $page, $this->url);
	}

	/**
	 * Returns a page link
	 */
	private function writePage($page, $class = null, $label = null, $disabled = false)
	{
		if ($class === null) {
			return sprintf(
				$this->template['page_empty'],
				sprintf(
					$this->template['link'],
					sprintf($this->url, $page),
					($label === null ? $page : $label)
				)
			);
		} else {
			return sprintf(
				$this->template['page'],
				$class,
				sprintf(
					$this->template['link'],
					sprintf($this->url, $page),
					($label === null ? $page : $label)
				)
			);
		}
	}

	/**
	 * Returns a label button
	 */
	private function writeLabel($label, $class = null)
	{
		$label = sprintf($this->template['link_empty'], $label);
		if ($class === null) {
			return sprintf(
				$this->template['page_empty'],
				$label
			);
		} else {
			return sprintf(
				$this->template['page'],
				$class,
				$label
			);
		}
	}

}
