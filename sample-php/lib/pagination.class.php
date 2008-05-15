<?php
/**
 * Pagination classs
 * zhuzhu@perlchina.org
 * $Id: pagination.class.php 10 2008-01-25 03:27:42Z zhuzhu $
 *
 */
class Pagination
{
	var $tot_results = 0;
    var $results_per_page = 1;
    var $current_page = 1;
    var $tot_pages = 1;
    var $current_result_start = 0;
    var $current_result_end = 0;
    var $baseUrl = '';
    var $previous = '&lt;';
    var $next = '&gt;';
    var $first = '&lt;&lt;';
    var $last = '&gt;&gt;';

    public function Pagination($current_page = 1, $tot_results = 0, $results_per_page = 1,
    	$baseUrl='')
    {
        $this->setCurrentPage($current_page);
        $this->setTotalResults($tot_results);
        $this->setResultsPerPage($results_per_page);
        $this->baseUrl = $baseUrl;
        $this->calculate();
    }

    private function setTotalResults($tot_results)
    {
        $tot_results = (int) $tot_results;
        if ($tot_results < 0) {
            $tot_results = 0;
        }
        $this->tot_results = $tot_results;
    }

    private function setResultsPerPage($results_per_page) {
        $results_per_page = (int) $results_per_page;
        if ($results_per_page < 1) {
            $results_per_page = 1;
        }
        $this->results_per_page = $results_per_page;
    }

    private function setCurrentPage($current_page)
    {
        $current_page = (int) $current_page;
        if ($current_page < 1) {
            $current_page = 1;
        }
        $this->current_page = (int) $current_page;
    }

    private function calculate()
    {
        if ($this->tot_results == 0) {
            $this->tot_pages = 1;
            $this->current_page = 1;
            $this->current_result_start = 0;
            $this->current_result_end = 0;
            return;
        }

        $this->tot_pages = (int)($this->tot_results / $this->results_per_page);
        if (($this->tot_results % $this->results_per_page) != 0) {
            $this->tot_pages++;
        }

        if ($this->current_page > $this->tot_pages) {
            $this->current_page = $this->tot_pages;
        }

        $result_end = $this->results_per_page * $this->current_page;
        if ($result_end > $this->tot_results) {
            $this->current_result_end = $this->tot_results;
        } else {
            $this->current_result_end = $result_end;
        }

        $this->current_result_start = $result_end - $this->results_per_page + 1;
    }
    
    public function pagers()
    {
    	$previousPage = $this->current_page-1;
    	$nextPage = $this->current_page+1;
    	
		$first= '<a href="'.$this->baseUrl.'&page=1">'.$this->first.'</a>';
		$previous = '<a href="'.$this->baseUrl.'&page=' . $previousPage .'">'.$this->previous.'</a>';
		$next = '<a href="'.$this->baseUrl.'&page=' . $nextPage .'">'.$this->next.'</a>';
		$last = '<a href="'.$this->baseUrl.'&page='.$this->tot_pages.'">'.$this->last.'</a>';
		
		$pagers = $first.' | '.$previous .' | '. $next .' | '.$last;
		return $pagers;
    }
}
?>