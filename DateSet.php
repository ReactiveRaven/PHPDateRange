<?php

require_once 'DateRange.php';

class DateSet
{
  const SORT_INSERTION = "SORT_INSERTION";
  const SORT_START_ASC = "SORT_START_ASC";
  const SORT_START_DESC = "SORT_START_DESC";
  const SORT_END_ASC = "SORT_END_ASC";
  const SORT_END_DESC = "SORT_END_DESC";
  const SORT_DATA_ASC = "SORT_DATA_ASC";
  const SORT_DATA_DESC = "SORT_DATA_DESC";
  
  private $dateRanges = array();
  
  public function __construct()
  {
  }
  
  /**
   * Add a new DateRange to the set
   * 
   * @param DateRange $newRange
   */
  public function addRange(DateRange $newRange) {
    $this->dateRanges[] = $newRange;
  }
  
  /**
   * Add multiple DateRange objects to the set
   * 
   * @param DateRange[] $newRanges
   * @throws InvalidArgumentException
   */
  public function addRanges($newRanges) {
    
    foreach ($newRanges as $newRange) {
      if (!$newRange instanceof DateRange) {
        throw new InvalidArgumentException("Expecting all entries in newRanges to be DateRange objects");
      }
    }
    
    foreach ($newRanges as $newRange) {
      $this->addRange($newRange);
    }
  }
  
  public function getSubsetContainedBy(DateRange $parentRange)
  {
    $result = new DateSet();
    
    foreach ($this->dateRanges as $childRange) 
    {
      if ($parentRange->contains($childRange)) {
        $result->addRange($childRange);
      }
    }
    
    return $result;
  }
  
  public function getSubsetContaining($date_or_range)
  {
    if (is_string($date_or_range)) {
      $date_or_range = new DateTime($date_or_range);
    }
    
    if ($date_or_range instanceof DateTime) {
      
    }
  }
  
  /**
   * Gets all the date ranges currently in the date set.
   * 
   * @return DateRange[]
   */
  public function getDateRanges()
  {
    $result = array();
    foreach ($this->dateRanges as $dateRange /* @var $dateRange DateRange */)
    {
      $result[] = clone $dateRange;
    }
    
    return $result;
  }
  
  public function getDateRangesData($sort_order = null)
  {
    $dateRanges = $this->dateRanges;
    
    if ($sort_order !== null && $sort_order !== self::SORT_INSERTION) {
      $dateRanges = $this->sort($dateRanges, $sort_order);
    }
    
    $result = array();
    foreach ($dateRanges as $dateRange /* @var $dateRange DateRange */)
    {
      $result[] = $dateRange->getData();
    }
    
    return $result;
  }
  
  private function sort($data, $sort_method) 
  {
    if (method_exists($this, strtolower($sort_method))) {
      return call_user_method(strtolower($sort_method), $this, $data);
    } 
    
    throw new InvalidArgumentException("Unrecognised sorting constant '" . $sort_method . "'");
  }
  
  private function sort_data_asc($data)
  {
    $output = array();
    $sortkeys = array();
    
    foreach ($data as $dateRange /* @var $dateRange DateRange */)
    {
      $output[] = $dateRange;
      $sortkeys[] = $dateRange->getData();
    }
    
    array_multisort($sortkeys, $output); 
    
    return $output;
  }
 
  private function sort_data_desc($data)
  {
    return array_reverse($this->sort_data_asc($data));
  }
  
  private function sort_start_asc($data)
  {
    $output = array();
    $sortkeys = array();
    
    foreach ($data as $dateRange /* @var $dateRange DateRange */)
    {
      $output[] = $dateRange;
      $sortkeys[] = $dateRange->getStart()->format("U");
    }
    
    array_multisort($sortkeys, $output); 
    
    return $output;
  }
  
  private function sort_start_desc($data)
  {
    return array_reverse($this->sort_start_asc($data));
  }
  
  private function sort_end_asc($data)
  {
    $output = array();
    $sortkeys = array();
    
    foreach ($data as $dateRange /* @var $dateRange DateRange */)
    {
      $output[] = $dateRange;
      $sortkeys[] = $dateRange->getEnd()->format("U");
    }
    
    array_multisort($sortkeys, $output); 
    
    return $output;
  }
  
  private function sort_end_desc($data)
  {
    return array_reverse($this->sort_end_asc($data));
  }
}