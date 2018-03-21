=== Plugin Name ===
Contributors: Udit Rawat
Donate link: 
Tags: exam matrix, exam, e exam, e-exam, online exam, quiz, online quiz, e quiz,e-quiz, wp quiz, wp test, online test
Requires at least: 3.0.1
Tested up to: 3.8
Stable tag: 1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Wordpress online quiz plugin for making unlimited online quiz and exam. 

== Description ==

it is a online quiz application where user can create unlimited Quiz with time limit and show them on your wordpress blog, 

the basic quiz structure is.

Quiz-->Set-->Subset-->Questions


It's very simple, just follow instruction below to create a quiz

* Create a set.
* Create a subset and choose apropriate set for it.
* Create number of questions for that subset/set
* Create a exam / quiz, set test to show instruction with total/per question marks, time limit and negative marking.
* Choose a set for that exam that's it.

**Import Export System**::-  
You can import question form csv format and also export question and results to csv file, don't forget to see import csv format before importing.

**Important**
1:- Please go for theme integration if test page is bank
2:- Update your permalink atleast once after you install plugin and it should be other then default structure

== Installation ==

* Install the plugin by uploading via wordpress and create quiz.
* Plugin also include 2 widget one for login which allow user to login
* And second for showing user profile and available exam for it.

**Adding theme support (optional)**

* create single-ex_test.php with your theme html structure
* copy template.inc from `plugins/exam-matrix/template`
* paste it in your theme folder
* include it in your single-ex_test.php 

ie.
`<?php require_once('template.inc'); ?>`



== Frequently Asked Questions ==

= Do i get a example theme for it ? =

Yes you can download simple theme form here `https://github.com/eklavyarawat/exam-matrix-theme`

== Screenshots ==

1. Add Set & Subset
2. Add Auestion
3. Manage Available Question Screen
4. Search Result Screen
5. Import Export Csv System
6. Quiz Basic Settings
