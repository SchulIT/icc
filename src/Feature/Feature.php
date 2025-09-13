<?php

namespace App\Feature;

enum Feature: string {
    case Wiki = "wiki";
    case Documents = "documents";
    case Privacy = "privacy";
    case LMS = "lms";
    case Chat = "chat";
    case ParentsDay = "parents_day";
    case StudentAbsence = "student_absence";
    case TeacherAbsence = "teacher_absence";
    case Checklists = "checklists";
    case Book = "book";
    case GradeBook = "grade_book";
    case Messages = "messages";
    case ReturnItem = "return_item";

    #[Requires(features: [Feature::LMS, Feature::Privacy], requirement: Requirement::Any)]
    case Consents = 'consents';
}