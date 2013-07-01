<?php

require_once 'UnitBase.php';

class DateRangeTest extends PHPUnit_Framework_TestCase
{
  
  public function testDateFormatsAccepted() {
    
    $testDate = "2000-01-01 23:59:59";
    
    $dateRange = new DateRange($testDate, $testDate);
    
    $this->assertEquals($testDate, $dateRange->getStart()->format("Y-m-d H:i:s"), "Expect matching string dates");
    
    $dateRange2 = new DateRange(new DateTime($testDate), new DateTime($testDate));
    
    $this->assertEquals($testDate, $dateRange2->getStart()->format("Y-m-d H:i:s"), "Expect matching DateTime dates");
    
    $dateRange3 = new DateRange($testDate . " + 1 day", $testDate . " + 1 day");
    
    $this->assertEquals("2000-01-02 23:59:59", $dateRange3->getStart()->format("Y-m-d H:i:s"), "Expect matching relative string dates");
  }
  
  public function testDatesImmutable()
  {
    $testDateTime = new DateTime("2000-01-01");
    
    $range = new DateRange($testDateTime, $testDateTime);
    
    $start = $range->getStart();
    $start->setDate(2010, 5, 5);
    
    $this->assertEquals($testDateTime->format("Y-m-d H:i:s"), $range->getStart()->format("Y-m-d H:i:s"), "Expect dates to be immutable once range created");
  }
  
  public function testSetGetData()
  {
    $dateRange = new DateRange("2000-01-01", "2000-01-01");
    $data = new stdClass();
    $data->fish = "chips";
    
    $this->assertNull($dateRange->getData(), "Data should be NULL when not specified in constructor");
    
    $dateRange->setData($data);
    
    $this->assertEquals($data, $dateRange->getData(), "Data should be updated by setData");
    
    $newDateRange = new DateRange("2000-01-01", "2000-01-01", $data);
    
    $this->assertEquals($data, $newDateRange->getData(), "Data should be set when specified in constructor");
  }
  
  public function testContains()
  {
    $dateRange = new DateRange("2000-01-01", "2001-01-01");
    $this->assertTrue($dateRange->contains("2000-06-01"), "Correctly contains a string date");
    $this->assertTrue($dateRange->contains(new DateTime("2000-06-01")), "Correctly contains a DateTime date");
    $this->assertTrue($dateRange->contains(new DateRange("2000-06-01", "2000-06-02")), "Correctly contains a DateRange");
    
    $this->assertFalse($dateRange->contains("1999-01-01"), "Correctly does not contain a string date that is too early");
    $this->assertFalse($dateRange->contains(new DateTime("1999-01-01")), "Correctly does not contain a DateTime date that is too early");
    $this->assertFalse($dateRange->contains(new DateRange("1999-01-01", "1999-01-02")), "Correctly does not contain a DateTime date that is too early");
    
    $this->assertFalse($dateRange->contains("2005-01-01"), "Correctly does not contain a string date that is too late");
    $this->assertFalse($dateRange->contains(new DateTime("2005-01-01")), "Correctly does not contain a DateTime date that is too late");
    $this->assertFalse($dateRange->contains(new DateRange("2005-01-01", "2005-01-01")), "Correctly does not contain a DateTime date that is too late");
    
    
    $this->assertFalse($dateRange->contains(new DateRange("1999-01-01", "2005-01-01")), "Correctly does not contain a DateRange that overlaps both ends");
    $this->assertFalse($dateRange->contains(new DateRange("1999-01-01", "2001-01-01")), "Correctly does not contain a DateRange that overlaps the start");
    $this->assertFalse($dateRange->contains(new DateRange("2000-01-01", "2005-01-01")), "Correctly does not contain a DateRange that overlaps the end");
    $this->assertTrue($dateRange->contains(new DateRange("2000-01-01", "2001-01-01")), "Correctly contains a DateRange that exactly matches");
  }
  
  public function testContainedBy()
  {
    $dateRange = new DateRange("2000-01-01", "2001-01-01");
    
    $this->assertTrue($dateRange->containedBy(new DateRange("1999-01-01", "2002-01-01")), "Correctly contained by a DateRange that overlaps both ends");
    $this->assertTrue($dateRange->containedBy(new DateRange("1999-01-01", "2001-01-01")), "Correctly contained by a DateRange that overlaps the start");
    $this->assertTrue($dateRange->containedBy(new DateRange("2000-01-01", "2002-01-01")), "Correctly contained by a DateRange that overlaps the end");
    $this->assertTrue($dateRange->containedBy(new DateRange("2000-01-01", "2001-01-01")), "Correctly contained by a DateRange that exactly matches");
    
    $this->assertFalse($dateRange->containedBy(new DateRange("2000-01-02", "2000-12-30")), "Correctly not contained by a DateRange that underlaps both ends");
    $this->assertFalse($dateRange->containedBy(new DateRange("2000-01-02", "2001-01-01")), "Correctly not contained by a DateRange that underlaps the start");
    $this->assertFalse($dateRange->containedBy(new DateRange("2000-01-01", "2000-12-30")), "Correctly not contained by a DateRange that underlaps the end");
  }
  
}