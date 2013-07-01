<?php

class DateRange
{
  protected $start = null;
  protected $end = null;
  protected $data = null;
  
  public function __construct($start, $end, $data = null)
  {
    if ($start instanceof DateTime) {
      $this->start = $start;
    } else {
      $this->start = new DateTime($start);
    }
    
    if ($end instanceof DateTime) {
      $this->end = $end;
    } else {
      $this->end = new DateTime($end);
    }
    
    $this->data = $data;
    
    if ($this->start > $this->end) {
      throw new InvalidArgumentException("Start must be before End");
    }
  }
  
  /**
   * @return DateTime the start of the range
   */
  public function getStart() 
  {
    return (clone $this->start);
  }
  
  /**
   * @return DateTime the end of the range
   */
  public function getEnd()
  {
    return (clone $this->end);
  }
  
  public function setTimezone($timezone) {
    $timezoneObject = $timezone;
    if (!$timezone instanceof DateTimeZone) {
      $timezoneObject = new DateTimeZone($timezone);
    }
    
    $this->start->setTimezone($timezoneObject);
    $this->end->setTimezone($timezoneObject);
    
    return $this;
  }
  
  public function setData($newData) 
  {
    $this->data = $newData;
  }
  
  public function getData()
  {
    return $this->data;
  }
  
  public function contains($date_or_range) {
    
    $result = null;
    
    if (is_string($date_or_range)) {
      $date_or_range = new DateTime($date_or_range);
    }
    
    if ($date_or_range instanceof DateTime) {
      $result = $this->containsDateTime($date_or_range);
    } else if ($date_or_range instanceof DateRange) {
      $result = $this->containsDateTime($date_or_range->start) && $this->contains($date_or_range->end);
    }
    
    if ($result === null) {
      throw new InvalidArgumentException("Expecting date_or_range to be DateTime, DateRange or date time string.");
    }
    
    return $result;
  }
  
  public function containsDateTime(DateTime $dateTime) 
  {
    return $this->start <= $dateTime && $this->end >= $dateTime; 
  }
  
  public function containedBy(DateRange $dateRange)
  {
    return $this->start >= $dateRange->start && $this->end <= $dateRange->end;
  }
}