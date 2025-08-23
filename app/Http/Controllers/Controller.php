<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Grocademy API",
 *     version="1.0",
 *     description="API Documentation for Grocademy"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
/**
 * @OA\Schema(
 * schema="Course",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="title", type="string", example="Advanced Web Dev"),
 * @OA\Property(property="description", type="string", example="Learn advanced web development techniques."),
 * @OA\Property(property="instructor", type="string", example="John Doe"),
 * @OA\Property(property="topics", type="array", @OA\Items(type="string"), example={"PHP", "Laravel"}),
 * @OA\Property(property="price", type="number", format="float", example=299000),
 * @OA\Property(property="thumbnail_image", type="string", format="url", example="http://example.com/image.jpg"),
 * @OA\Property(property="total_modules", type="integer", example=10),
 * @OA\Property(property="created_at", type="string", format="date-time"),
 * @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 * schema="Module",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="course_id", type="integer", example=1),
 * @OA\Property(property="title", type="string", example="Introduction"),
 * @OA\Property(property="description", type="string", example="Module description."),
 * @OA\Property(property="order", type="integer", example=1),
 * @OA\Property(property="pdf_content", type="string", format="url"),
 * @OA\Property(property="video_content", type="string", format="url"),
 * @OA\Property(property="created_at", type="string", format="date-time"),
 * @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 * schema="User",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="username", type="string", example="johndoe"),
 * @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 * @OA\Property(property="first_name", type="string", example="John"),
 * @OA\Property(property="last_name", type="string", example="Doe"),
 * @OA\Property(property="balance", type="number", format="float", example=100000),
 * @OA\Property(property="courses_purchased", type="integer", example=2)
 * )
 *
 * @OA\Schema(
 * schema="Pagination",
 * @OA\Property(property="current_page", type="integer"),
 * @OA\Property(property="total_pages", type="integer"),
 * @OA\Property(property="total_items", type="integer")
 * )
 * * @OA\Schema(
 * schema="Quiz",
 * @OA\Property(property="id", type="integer"),
 * @OA\Property(property="module_id", type="integer"),
 * @OA\Property(property="title", type="string"),
 * @OA\Property(property="description", type="string"),
 * @OA\Property(property="passing_score", type="integer")
 * )
 * * @OA\Schema(
 * schema="Question",
 * @OA\Property(property="id", type="integer"),
 * @OA\Property(property="quiz_id", type="integer"),
 * @OA\Property(property="question_text", type="string"),
 * @OA\Property(property="answers", type="array", @OA\Items(ref="#/components/schemas/Answer"))
 * )
 * * @OA\Schema(
 * schema="Answer",
 * @OA\Property(property="id", type="integer"),
 * @OA\Property(property="question_id", type="integer"),
 * @OA\Property(property="answer_text", type="string"),
 * @OA\Property(property="is_correct", type="boolean")
 * )
 */
abstract class Controller
{
    //
}
