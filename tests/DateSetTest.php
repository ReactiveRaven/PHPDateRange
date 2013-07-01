<?php

require_once 'UnitBase.php';

class DateSetTest extends PHPUnit_Framework_TestCase
{
  
  public function getDateSet($data = null)
  { 
    $set = new DateSet();
    if ($data === null)
    {
      $data = $this->getUbuntuData();
    }
    foreach ($data as $bits)
    {
      list($start, $end, $value) = $bits;
      $set->addRange(new DateRange($start, $end, $value));
    }
    return $set;
  }
  
  public function getDateSetShuffled()
  {
    $data = $this->getUbuntuData();
    shuffle($data);
    
    return $this->getDateSet($data);
  }
  
  
  private function getUbuntuData()
  {
    return array(
      array("2005-10-12", "2007-04-01", "Breezy Badger"),
      array("2006-06-01", "2009-07-14", "Dapper Drake"),
      array("2006-10-18", "2008-04-01", "Edgy Eft"),
      array("2007-04-19", "2008-10-01", "Feisty Fawn"),
      array("2007-10-18", "2009-04-18", "Gutsy Gibbon"),
      array("2008-04-28", "2011-05-12", "Hardy Heron"),
      array("2008-10-30", "2010-04-30", "Intrpid Ibex"),
      array("2009-04-23", "2010-10-23", "Jaunty Jackalope"),
      array("2009-10-29", "2011-04-30", "Karmic Koala"),
      array("2012-02-16", "2013-05-09", "Lucid Lynx"),
      array("2010-10-10", "2012-04-10", "Maverick Meerkat"),
      array("2011-04-28", "2012-10-28", "Natty Narwhal"),
      array("2011-10-13", "2013-05-09", "Oneiric Ocelot"),
      array("2012-04-26", "2017-04-01", "Precise Pangolin"),
      array("2012-10-18", "2014-04-01", "Quantal Quetzal"),
      array("2013-04-25", "2014-01-01", "Raring Ringtail"),
      array("2013-10-01", "2014-07-01", "Saucy Salamander"),
    );
  }
  
  public function testSubsetContainedBy() 
  {
    $set = $this->getDateSet();
    
    $whole = $set->getSubsetContainedBy(new DateRange("2000-01-01", "2050-01-01"));
    $this->assertEquals(count($set->getDateRanges()), count($whole->getDateRanges()), "Expecting all entries to be retained when wholly contained");
    
    $whole->addRange(new DateRange("2000-01-01", "2000-01-01", "extra"));
    $this->assertEquals(count($set->getDateRanges()) + 1, count($whole->getDateRanges()), "Expect new set to be separate from original");
    
    $single = $set->getSubsetContainedBy(new DateRange("2000-01-01", "2006-01-01"));
    $this->assertEquals("Breezy Badger", implode("", $single->getDateRangesData()), "Expect to get the first entry");
    
    $half = $set->getSubsetContainedBy(new DateRange("2000-01-01", "2009-01-01"));
    $this->assertEqual(7, count($half->getDateRanges()), "Expect to get the first seven entries");
    
    $none_low = $set->getSubsetContainedBy(new DateRange("2000-01-01", "2000-01-02"));
    $this->assertEquals(0, count($none_low), "Expect an empty set as range too low");
    
    $none_mid = $set->getSubsetContainedBy(new DateRange("2010-03-01", "2010-04-12"));
    $this->assertEquals(0, count($none_mid), "Expect an empty set as none contained in given range");
    
    $none_overlap = $set->getSubsetContainedBy(new DateRange("2000-01-01", "2005-10-13"));
    $this->assertEquals(0, count($none_overlap), "Expect an empty set as although overlapping, does not wholy contain any of input set");
    
    $none_high = $set->getSubsetContainedBy(new DateRange("2050-01-01", "2050-01-02"));
    $this->assertEquals(0, count($none_high), "Expect an empty set as range too high");
  }
  
  public function testSorting()
  {
    $ubuntuData = $this->getUbuntuData();
    shuffle($ubuntuData);
    
    $set = $this->getDateSet($ubuntuData);
    
    $ubuntuStrings = array();
    foreach ($ubuntuData as $bits) {
      $ubuntuStrings[] = $bits[2];
    }
    
    $sortedUbuntuStrings = $ubuntuStrings;
    sort($sortedUbuntuStrings);
    
    $ubuntuStringsEndOrder = array(
      "Breezy Badger",
      "Edgy Eft",
      "Feisty Fawn",
      "Dapper Drake",
      "Gutsy Gibbon",
      "Intrpid Ibex",
      "Jaunty Jackalope",
      "Karmic Koala",
      "Hardy Heron",
      "Maverick Meerkat",
      "Natty Narwhal",
      "Lucid Lynx",
      "Oneiric Ocelot",
      "Raring Ringtail",
      "Quantal Quetzal",
      "Saucy Salamander",
      "Precise Pangolin"
    );
    
    $this->assertEquals(join("\n", $set->getDateRangesData()), join("\n", $ubuntuStrings), "Expect in insertion order by default");
    $this->assertEquals(join("\n", $set->getDateRangesData(DateSet::SORT_INSERTION)), join("\n", $ubuntuStrings), "Expect in insertion order by default");
    
    $this->assertEquals(join("\n", $set->getDateRangesData(DateSet::SORT_START_ASC)), join("\n", $sortedUbuntuStrings), "Expect in SORT_START_ASC when requested");
    $this->assertEquals(join("\n", $set->getDateRangesData(DateSet::SORT_START_DESC)), join("\n", array_reverse($sortedUbuntuStrings)), "Expect in SORT_START_DESC when requested");
    
    $this->assertEquals(join("\n", $set->getDateRangesData(DateSet::SORT_END_ASC)), join("\n", $ubuntuStringsEndOrder), "Expect in SORT_END_ASC when requested");
    $this->assertEquals(join("\n", $set->getDateRangesData(DateSet::SORT_END_DESC)), join("\n", array_reverse($ubuntuStringsEndOrder)), "Expect in SORT_END_DESC when requested");
    
    $this->assertEquals(join("\n", $set->getDateRangesData(DateSet::SORT_DATA_ASC)), join("\n", $sortedUbuntuStrings), "Expect in SORT_DATA_ASC when requested");
    $this->assertEquals(join("\n", $set->getDateRangesData(DateSet::SORT_DATA_DESC)), join("\n", array_reverse($sortedUbuntuStrings)), "Expect in SORT_DATA_DESC when requested");
    
  }
}
