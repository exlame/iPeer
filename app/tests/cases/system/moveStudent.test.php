<?php
require_once('PHPWebDriver/WebDriver.php');
require_once('PHPWebDriver/WebDriverBy.php');
require_once('PHPWebDriver/WebDriverWait.php');
require_once('PageFactory.php');

class MoveStudentTestCase extends CakeTestCase
{
    protected $web_driver;
    protected $session;
    protected $url = 'http://ipeerdev.ctlt.ubc.ca/';
    
    public function startCase()
    {
        $wd_host = 'http://localhost:4444/wd/hub';
        $this->web_driver = new PHPWebDriver_WebDriver($wd_host);
        $this->session = $this->web_driver->session('firefox');
        $this->session->open($this->url);
        
        $w = new PHPWebDriver_WebDriverWait($this->session);
        $this->session->deleteAllCookies();
        $login = PageFactory::initElements($this->session, 'Login');
        $home = $login->login('admin1', 'ipeeripeer');
    }
    
    public function endCase()
    {
        $this->session->deleteAllCookies();
        $this->session->close();
    }
    
    public function testAddSurveyEvent()
    {
        $this->session->element(PHPWebDriver_WebDriverBy::LINK_TEXT, 'Courses')->click();
        $title = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "h1.title")->text();
        $this->assertEqual($title, 'Courses');
        
        $this->session->element(PHPWebDriver_WebDriverBy::LINK_TEXT, 'APSC 201')->click();
        $title = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "h1.title")->text();
        $this->assertEqual($title, 'APSC 201 - Technical Communication');
        
        $this->session->element(PHPWebDriver_WebDriverBy::LINK_TEXT, 'Add Event')->click();
        $title = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "h1.title")->text();
        $this->assertEqual($title, 'APSC 201 - Technical Communication > Add Event');
        
        $title = $this->session->element(PHPWebDriver_WebDriverBy::ID, 'EventTitle');
        $title->sendKeys('Group Making Survey');
        
        $desc = $this->session->element(PHPWebDriver_WebDriverBy::ID, 'EventDescription');
        $desc->sendKeys('This survey is for creating groups.');
        
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 
            'select[id="EventEventTemplateTypeId"] option[value="3"]')->click();
        
        $this->session->element(PHPWebDriver_WebDriverBy::ID, 'EventDueDate')->click();
        $this->session->element(PHPWebDriver_WebDriverBy::ID, 'EventReleaseDateBegin')->click();
        $this->session->element(PHPWebDriver_WebDriverBy::ID, 'EventReleaseDateEnd')->click();
        
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'input[type="submit"]')->click();
        $msg = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']")->text();
        $this->assertEqual($msg, 'Add event successful!'); 
    }
    
    public function testmoveStudent()
    {
        $this->session->open($this->url.'courses/move');
        $sourceCourse = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="CourseSourceCourses"] option[value="1"]');
        $sourceCourse->click();
        $this->assertEqual($sourceCourse->text(), 'MECH 328 - Mechanical Engineering Design Project');
        // need to wait for the next drop down menu to populate
        $w = new PHPWebDriver_WebDriverWait($this->session);
        $session = $this->session;
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="CourseSourceSurveys"] option')) - 1;
            }
        );
        
        $sourceSurvey = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="CourseSourceSurveys"] option[value="4"]');
        $sourceSurvey->click();
        $this->assertEqual($sourceSurvey->text(), 'Team Creation Survey');
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="CourseSubmitters"] option')) - 1;
            }
        );
        
        $submitter = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="CourseSubmitters"] option[value="31"]');
        $submitter->click();
        $this->assertEqual($submitter->text(), 'Hui Student');
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="CourseDestCourses"] option')) - 1;
            }
        );
        
        $destCourse = $this->session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="CourseDestCourses"] option');
        $this->assertEqual($destCourse[0]->text(), '-- Pick a course --');
        $this->assertEqual($destCourse[1]->text(), 'APSC 201 - Technical Communication');
        $courseId = $destCourse[1]->attribute('value');
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="CourseDestCourses"] option[value="'.$courseId.'"]')->click();
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="CourseDestSurveys"] option')) - 1;
            }
        );        

        $destSurvey = $this->session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="CourseDestSurveys"] option');
        $this->assertEqual($destSurvey[0]->text(), '-- Pick a survey --');
        $this->assertEqual($destSurvey[1]->text(), 'Group Making Survey');
        $eventId = $destSurvey[1]->attribute('value');
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="CourseDestSurveys"] option[value="'.$eventId.'"]')->click();
        
        $this->session->element(PHPWebDriver_WebDriverBy::ID, 'CourseAction0')->click();
        
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'input[type="submit"]')->click();
        $msg = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']")->text();
        $this->assertEqual($msg, 'Hui Student was successfully copied to APSC 201 - Technical Communication.');
        
        $this->deleteEvent($eventId);
    }
    
    public function deleteEvent($eventId)
    {
        $this->session->open($this->url.'evaluations/viewSurveySummary/'.$eventId);
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'input[aria-controls="individualResponses"]')->sendKeys('Hui');
        $result = $this->session->element(PHPWebDriver_WebDriverBy::LINK_TEXT, 'Result');
        $this->session->open($result->attribute('href')); // instead of trying to go to the new window or tab
        $title = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "h1.title")->text();
        $this->assertEqual($title, 'APSC 201 - Technical Communication > Group Making Survey > Results');
        
        // unenrol Hui Student
        $this->session->open($this->url.'users/goToClassList/2');
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'input[aria-controls="table_id"]')->sendKeys('Hui');
        $w = new PHPWebDriver_WebDriverWait($this->session);
        $session = $this->session;
        $w->until(
            function($session) {
                $count = count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'tr[class="odd"]'));
                return ($count == 1);
            }
        );
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'td[class="  sorting_1"]')->click();
        $this->session->element(PHPWebDriver_WebDriverBy::LINK_TEXT, 'Drop')->click();
        $this->session->accept_alert();
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']"));
            }
        );
        $msg = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']")->text();
        $this->assertEqual($msg, 'Student is successfully unenrolled!');
        
        // delete Group Making Survey Event
        $this->session->open($this->url.'events/delete/'.$eventId);
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']"));
            }
        );
        $msg = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']")->text();
        $this->assertEqual($msg, 'The event has been deleted successfully.');
    }
    
}