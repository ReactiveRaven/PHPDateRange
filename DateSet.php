<?php

require_once 'DateRange.php';

class DateSet
{
  const SORT_INSERTION = "insertion";
  const SORT_START_ASC = "start";
  const SORT_START_DESC = "trats";
  const SORT_END_ASC = "end";
  const SORT_END_DESC = "dne";
  const SORT_DATA_ASC = "data";
  const SORT_DATA_DESC = "atad";
  
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
  
  public function getDateRangesData()
  {
    $result = array();
    foreach ($this->dateRanges as $dateRange /* @var $dateRange DateRange */)
    {
      $result[] = $dateRange->getData();
    }
    
    return $result;
  }
}