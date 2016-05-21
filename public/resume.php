<?php
$currentpage = __FILE__;
require_once('../private/Main.php');//MUST be imported.
import('Page.Page');

$page = Page::getPage();
//Begin Page Processing

import('Template.Template');
try {
    $template = Template::getSystemTemplate('Resume');
} catch (Exception $x) {
    
}
$page->setTemplate($template);
$page->startPage();
$page->setTitle('Dominic Masters');
$page->setTitlePostfix('');

$page->echoData('<div class="page" size="A4">'); {
    $page->echoData('<div class="col-4">'); {
        //$page->echoData('<img src="http://placehold.it/512x512" class="img-responsive" />');
        $page->echoData('<img src="'.File::getCurrentDirectory()->getChild('images')->getChild('profile.jpg')->getAsHTTPPath().'" class="img-responsive profile-img" />');
    }
    $page->echoData('</div>');

    $page->echoData('<div class="col-6">'); {
        $page->echoData('<div class="col-10 text-right"> <a href="#" class="hide-print" onclick="window.print();"><i class="fa fa-print"></i> Print</a></div>');
        $page->echoData('<div class="col-10">&nbsp;</div>');
        $page->echoData('<div class="col-5">'); {

            $page->echoData('<div class="col-3 text-right padded"><i class="fa fa-globe"></i></div>');
            $page->echoData('<div class="col-7 padded"><a href="http://domsplace.com">http://domsplace.com</a></div>');

            $page->echoData('<div class="col-10">&nbsp;</div>');

            $page->echoData('<div class="col-3 text-right padded"><i class="fa fa-envelope"></i></div>');
            $page->echoData('<div class="col-7 padded"><a href="mailto:dominic@domsplace.com">dominic@domsplace.com</a></div>');
        }
        $page->echoData('</div>');

        $page->echoData('<div class="col-5">'); {
            $page->echoData('<div class="col-3 text-right padded"><i class="fa fa-mobile"></i></div>');
            $page->echoData('<div class="col-7 padded"><a href="tel:0417056000">0417056000</a></div>');

            $page->echoData('<div class="col-10">&nbsp;</div>');

            $page->echoData('<div class="col-3 text-right padded"><i class="fa fa-linkedin-square"></i></div>');
            $page->echoData('<div class="col-7 padded"><a href="'.getconf('LINKEDIN_URL').'">LinkedIn</a></div>');
        }
        $page->echoData('</div>');
        
        $page->echoData('<div class="col-10"><br /></div>');
        $page->echoData('<div class="col-10 padded">'); {
            $page->echoData('<h1 class="person-name">Dominic Masters</h1>');
            $page->echoData('<p>');{
                $page->echoData('Hello, my name is Dominic Masters. I am a ');
                $datetime1 = new DateTime();
                $datetime2 = new DateTime('1994-11-15');
                $interval = $datetime1->diff($datetime2);
                $page->echoData($interval->format('%Y'));
                $page->echoData(' year old programmer from Australia. I have been');
                $page->echoData(' programming as long as I can remember. After completing my'
                        . ' studies at Southern Cross University'
                        . ' I managed IT solutions, focusing mainly on solutions development for'
                        . ' Market Direct Camper Trailers and Offroad Caravans\' website.');
            }
            $page->echoData('</p>');
        }
        $page->echoData('</div>');
    }
    $page->echoData('</div>');

    $page->echoData('<div class="col-10"></div>');

    $page->echoData('<div class="col-5">'); {
        $page->echoData('<h1>Qualifications</h1>');
        
        //Panels
        $page->echoData('<div class="panel">');
        $page->echoData('<div class="title"><i class="fa fa-graduation-cap"></i> Bachelor of Applied Computing</div>');
        $page->echoData('<div class="col-3 text-right padded"><strong>Alma mater</strong></div>');
        $page->echoData('<div class="col-7 padded">Southern Cross University</div>');
        //$page->echoData('<p>');
        $page->echoData('<div class="col-3 text-right padded"><strong>Duration</strong></div>');
        $page->echoData('<div class="col-7 padded">Feb. 2013 to Apr. 2014</div>');
        
        $page->echoData('<div class="col-3 text-right padded"><strong>Grade</strong></div>');
        $page->echoData('<div class="col-7 padded">Distinction</div>');
        //$page->echoData('</p>');
        $page->echoData('<div class="col-fix"></div>');
        $page->echoData('</div>');
        
        $page->echoData('<div class="panel">');
        $page->echoData('<div class="title"><i class="fa fa-book"></i> Diploma of IT</div>');
        $page->echoData('<div class="col-3 text-right padded"><strong>Alma mater</strong></div>');
        $page->echoData('<div class="col-7 padded">North Coast TAFE</div>');
        $page->echoData('<div class="col-3 text-right padded"><strong>Duration</strong></div>');
        $page->echoData('<div class="col-7 padded">Feb. 2012 to Nov. 2012</div>');
        $page->echoData('<div class="col-3 text-right padded"><strong>Grade</strong></div>');
        $page->echoData('<div class="col-7 padded">Distinction</div>');
        $page->echoData('<div class="col-fix"></div>');
        $page->echoData('</div>');
        
        $page->echoData('<div class="panel">');
        $page->echoData('<div class="title"><i class="fa fa-pencil"></i> Certificate IV of IT</div>');
        $page->echoData('<div class="col-3 text-right padded"><strong>Alma mater</strong></div>');
        $page->echoData('<div class="col-7 padded">North Coast TAFE</div>');
        $page->echoData('<div class="col-3 text-right padded"><strong>Duration</strong></div>');
        $page->echoData('<div class="col-7 padded">Feb. 2011 to Dec. 2011</div>');
        $page->echoData('<div class="col-3 text-right padded"><strong>Grade</strong></div>');
        $page->echoData('<div class="col-7 padded">Distinction</div>');
        $page->echoData('<div class="col-fix"></div>');
        $page->echoData('</div>');
        
        $page->echoData('<br /><h1>Experience</h1>');
        
        $page->echoData('<div class="panel">');
        $page->echoData('<div class="title"><i class="fa fa-suitcase"></i> Market Direct Campers</div>');
        $page->echoData('<div class="col-4 text-right padded"><strong>Duration</strong></div>');
        $page->echoData('<div class="col-6 padded">Jul. 2015 to Present</div>');
        $page->echoData('<div class="col-4 text-right padded"><strong>Responsibilities</strong></div>');
        $page->echoData('<div class="col-6 padded">');{
            $page->echoData('<ul>');
            $page->echoData('<li>Update/Design website</li>');
            $page->echoData('<li>Maintain Remote Server</li>');
            $page->echoData('<li>IT Support</li>');
            $page->echoData('<li>Track Analytics</li>');
            $page->echoData('<li>Marketing Techniques through Digital Media</li>');
            $page->echoData('</ul>');
        }
        $page->echoData('</div>');
        $page->echoData('<div class="col-4 text-right padded"></div>');
        $page->echoData('<div class="col-6 padded"><a href="http://marketdirect.com.au">http://marketdirect.com.au</a></div>');
        $page->echoData('<div class="col-fix"></div>');
        $page->echoData('</div>');
    }
    
    $page->echoData('</div>');

    $page->echoData('<div class="col-5">'); {
        $page->echoData('<h1>Skills</h1>');
        $page->echoData('<div class="col-10 padded hide-print"><small>Hover for additional information</small><br /><br /></div>');
        $page->echoData('<div class="col-10 padded">');{
            $page->echoData('<div class="chart">');
            $page->echoData('<div class="line" style="width: 80%;"><span class="label">Programming</span><div class="node">Anything from websites, to mobile apps, to games and software.</div></div>');
            $page->echoData('<div class="line" style="width: 75%;"><span class="label">Web Design</span><div class="node">Beautiful, efficient, clean and professional web designs, made with the latest industry leading standards.</div></div>');
            $page->echoData('<div class="line" style="width: 70%;"><span class="label">Teamwork</span><div class="node">Share, as well as take-in, ideas and experiences of fellow peers.</div></div>');
            $page->echoData('<div class="line" style="width: 65%;"><span class="label">Systems Administration</span><div class="node">Managing, Implementing and Upgrading Server Systems and software. Including rapid issue identification and resolution.</div></div>');
            $page->echoData('<div class="line" style="width: 50%;"><span class="label">Database Management</span><div class="node">Design, deploy and manage small to large database systems, enhancing security and efficiency wherever possible.</div></div>');
            $page->echoData('<div class="line" style="width: 45%;"><span class="label">Media &amp; Marketing</span><div class="node">From editing and desinging videos, through to marketing and development of images, banners, documentation and more.</div></div>');
            
            $page->echoData('</div>');
        }
        $page->echoData('</div>');
        $page->echoData('');
    }
    $page->echoData('</div>');

    $page->echoData('<div class="col-5">'); {
        $page->echoData('<br /><h1>Goals</h1>');
        $page->echoData('<div class="col-10 padded">');
        $page->echoData('<ul>');
        $page->echoData('<li>Keep up to date with emerging web technologies (Cordova, AnguarJS, Meteor etc.)</li>');
        $page->echoData('<li>Develop larger knowledgebase of software languages</li>');
        $page->echoData('<li>Design and develop apps for mobile devices</li>');
        $page->echoData('<li>Gain experience in real world I.T.</li>');
        $page->echoData('</ul>');
        $page->echoData('</div>');
    }
    $page->echoData('</div>');
}
$page->echoData('</div>');

$page->echoData('<div class="page" size="A4" >'); {

    $page->echoData('<div class="col-5">'); {
        $page->echoData('<h1>References</h1>');
        $page->echoData('<div class="col-10">');{
            $page->echoData('<div class="panel">');
            $page->echoData('<div class="title">Dr William Smart</div>');
            $page->echoData('<div class="col-10 padded">Lecturer in Information Technology</div>');
            $page->echoData('<div class="col-10 padded">Southern Cross University</div>');
            $page->echoData('<div class="col-2 padded text-right"><i class="fa fa-phone"></i></div><div class="col-8 padded">07 55893121</div>');
            $page->echoData('<div class="col-2 padded text-right"><i class="fa fa-mobile"></i></div><div class="col-8 padded">04 48924574</div>');
            $page->echoData('<div class="col-fix"></div>');
            $page->echoData('</div>');
            
            $page->echoData('<div class="panel">');
            $page->echoData('<div class="title">Mr Scott Martin</div>');
            $page->echoData('<div class="col-10 padded">TAFE Teacher</div>');
            $page->echoData('<div class="col-10 padded">North Coast Institute of TAFE</div>');
            $page->echoData('<div class="col-2 padded text-right"><i class="fa fa-phone"></i></div><div class="col-8 padded">02 66747333</div>');
            $page->echoData('<div class="col-fix"></div>');
            $page->echoData('</div>');
            
            $page->echoData('<div class="panel">');
            $page->echoData('<div class="title">Mr David Willis</div>');
            $page->echoData('<div class="col-10 padded">IT Student</div>');
            $page->echoData('<div class="col-2 padded text-right"><i class="fa fa-mobile"></i></div><div class="col-8 padded">04 05207348</div>');
            $page->echoData('<div class="col-fix"></div>');
            $page->echoData('</div>');
            
            $page->echoData('<div class="panel">');
            $page->echoData('<div class="title">Mr Daniel Hughes</div>');
            $page->echoData('<div class="col-10 padded">Corporate Champions Project Manager</div>');
            $page->echoData('<div class="col-2 padded text-right"><i class="fa fa-phone"></i></div><div class="col-8 padded">02 8188 2373</div>');
            $page->echoData('<div class="col-2 padded text-right"><i class="fa fa-mobile"></i></div><div class="col-8 padded">04 16227409</div>');
            $page->echoData('<div class="col-fix"></div>');
            $page->echoData('</div>');
        }
        $page->echoData('</div>');
    }
    $page->echoData('</div>');

    
}
$page->echoData('</div>');


//End Page Processing
$page->makePage();
$page->endPage();
