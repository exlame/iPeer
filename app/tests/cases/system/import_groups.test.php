<?php
require_once('system_base.php');

class ImportGroupsTestCase extends SystemBaseTestCase
{    
    public function startCase()
    {
        $this->getUrl();
        $wd_host = 'http://localhost:4444/wd/hub';
        $this->web_driver = new PHPWebDriver_WebDriver($wd_host);
        $this->session = $this->web_driver->session('firefox');
        $this->session->open($this->url);
        
        $w = new PHPWebDriver_WebDriverWait($this->session);
        $this->session->deleteAllCookies();
        $login = PageFactory::initElements($this->session, 'Login');
        $home = $login->login('instructor2', 'ipeeripeer');
    }
    
    public function endCase()
    {
        $this->session->deleteAllCookies();
        $this->session->close();
    }
    
    public function testImportGroups()
    {
        $this->session->open($this->url.'groups/import/2');
        $file = $this->session->element(PHPWebDriver_WebDriverBy::ID, 'GroupFile');
        $file->sendKeys(dirname(__FILE__).'/files/importGroup.csv');
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'input[type="submit"]')->click();
        $w = new PHPWebDriver_WebDriverWait($this->session);
        $session = $this->session;
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[id='title']"));
            }
        );
        $msg = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[id='title']")->text();
        $this->assertEqual($msg, 'The group CSV file was processed.');
        
        $this->session->open($this->url.'courses/home/2');
        $this->session->element(PHPWebDriver_WebDriverBy::LINK_TEXT, 'List Groups')->click();
        $this->session->element(PHPWebDriver_WebDriverBy::LINK_TEXT, 'Team (3 members)')->click();
        $groupId = end(explode('/', $this->session->url()));
        
        $this->session->open($this->url.'groups/edit/'.$groupId);
        $groupName = $this->session->element(PHPWebDriver_WebDriverBy::ID, 'GroupGroupName')->attribute('value');
        $this->assertEqual($groupName, 'Team');
        $inGroup = count($this->session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[id="selected_groups"] option'));
        $this->assertEqual($inGroup, 3);
        
        $this->session->open($this->url.'groups/delete/'.$groupId);
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']"));
            }
        );
        $msg = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']")->text();
        $this->assertEqual($msg, 'The group was deleted successfully.');
    }
    
    public function testImportInvalidUsers()
    {
        $this->waitForLogoutLogin('instructor1');
        $this->session->open($this->url.'courses/home/1');
        $this->session->open($this->url.'groups/import/1');
        $file = $this->session->element(PHPWebDriver_WebDriverBy::ID, 'GroupFile');
        // tests nonexistant users, unenrolled students/tutors, import user of a different role (eg. instructor)
        $file->sendKeys(dirname(__FILE__).'/files/invalidGroupMembers.csv');
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'input[type="submit"]')->click();
        $w = new PHPWebDriver_WebDriverWait($this->session);
        $session = $this->session;
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[id='title']"));
            }
        );
        $msg = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[id='title']")->text();
        $this->assertEqual($msg, 'The group CSV file was processed.');
        
        $group = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[1]/tbody/tr[2]/td')->text();
        $this->assertEqual($group, 'Team Supreme');
        $title = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/h2[2]')->text();
        $this->assertEqual($title, 'Students Not Placed');
        $user = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[2]/tbody/tr[2]/td[1]')->text();
        $this->assertEqual($user, 'instructor1');
        $user = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[2]/tbody/tr[3]/td[1]')->text();
        $this->assertEqual($user, 'redshirt0004');
        $user = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[2]/tbody/tr[4]/td[1]')->text();
        $this->assertEqual($user, 'redshirt0005');
        $user = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[2]/tbody/tr[5]/td[1]')->text();
        $this->assertEqual($user, 'root');
        $user = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[2]/tbody/tr[6]/td[1]')->text();
        $this->assertEqual($user, 'tutor3');
        $user = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[2]/tbody/tr[7]/td[1]')->text();
        $this->assertEqual($user, 'redshirt9998');
        $user = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[2]/tbody/tr[8]/td[1]')->text();
        $this->assertEqual($user, 'redshirt9999');        
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'input[value="OK"]')->click();
        
        $this->session->element(PHPWebDriver_WebDriverBy::LINK_TEXT, 'Team Supreme (0 member)')->click();
        $this->session->open(str_replace('view', 'delete', $this->session->url()));
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']"));
            }
        );
        $msg = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']")->text();
        $this->assertEqual($msg, 'The group was deleted successfully.');
    }
    
    public function testImportWithStudentNo()
    {
        $this->session->open($this->url.'groups/import/1');
        $file = $this->session->element(PHPWebDriver_WebDriverBy::ID, 'GroupFile');
        // students identified with student numbers
        $file->sendKeys(dirname(__FILE__).'/files/groupsStudentNo.csv');
        $this->session->element(PHPWebDriver_WebDriverBy::ID, 'GroupIdentifiersStudentNo')->click();
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'input[type="submit"]')->click();
        $w = new PHPWebDriver_WebDriverWait($this->session);
        $session = $this->session;
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[id='title']"));
            }
        );
        $msg = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[id='title']")->text();
        $this->assertEqual($msg, 'The group CSV file was processed.');

        $group = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[1]/tbody/tr[2]/td')->text();
        $this->assertEqual($group, 'Best Team');
        $title = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/h2[2]')->text();
        $this->assertEqual($title, 'Students Placed');
        $user = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[2]/tbody/tr[2]/td[1]')->text();
        $this->assertEqual($user, '65498451');
        $user = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[2]/tbody/tr[3]/td[1]')->text();
        $this->assertEqual($user, '65468188');
        $user = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[2]/tbody/tr[4]/td[1]')->text();
        $this->assertEqual($user, '98985481');
        $user = $this->session->element(PHPWebDriver_WebDriverBy::XPATH, '//*[@id="groupsimport"]/table[2]/tbody/tr[5]/td[1]')->text();
        $this->assertEqual($user, '84188465');
        $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'input[value="OK"]')->click();
        
        $this->session->element(PHPWebDriver_WebDriverBy::LINK_TEXT, 'Best Team (4 members)')->click();
        $this->session->open(str_replace('view', 'delete', $this->session->url()));
        $w->until(
            function($session) {
                return count($session->elements(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']"));
            }
        );
        $msg = $this->session->element(PHPWebDriver_WebDriverBy::CSS_SELECTOR, "div[class='message good-message green']")->text();
        $this->assertEqual($msg, 'The group was deleted successfully.');
    }
}