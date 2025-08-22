<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CaptchaService
{
    /**
     * Generate a new captcha and store it in session
     *
     * @return array
     */
    public static function generate()
    {
        $num1 = rand(1, 20);
        $num2 = rand(1, 20);
        $operators = ['+', '-', '*'];
        $operator = $operators[array_rand($operators)];
        
        $question = "$num1 $operator $num2";
        $answer = self::calculateAnswer($num1, $num2, $operator);
        
        // Store in session with a unique key
        $captchaKey = 'captcha_' . uniqid();
        Session::put($captchaKey, $answer);
        Session::put($captchaKey . '_question', $question);
        
        return [
            'key' => $captchaKey,
            'question' => $question,
            'answer' => $answer
        ];
    }
    
    /**
     * Verify the captcha answer
     *
     * @param string $captchaKey
     * @param string $userAnswer
     * @return bool
     */
    public static function verify($captchaKey, $userAnswer)
    {
        $correctAnswer = Session::get($captchaKey);
        
        if ($correctAnswer === null) {
            return false;
        }
        
        // Clean up session
        Session::forget($captchaKey);
        Session::forget($captchaKey . '_question');
        
        return (int)$userAnswer === (int)$correctAnswer;
    }
    
    /**
     * Calculate the answer for the mathematical operation
     *
     * @param int $num1
     * @param int $num2
     * @param string $operator
     * @return int
     */
    private static function calculateAnswer($num1, $num2, $operator)
    {
        switch ($operator) {
            case '+':
                return $num1 + $num2;
            case '-':
                return $num1 - $num2;
            case '*':
                return $num1 * $num2;
            default:
                return $num1 + $num2;
        }
    }
    
    /**
     * Get a new captcha for AJAX requests
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function getNewCaptcha()
    {
        $captcha = self::generate();
        
        return response()->json([
            'success' => true,
            'captcha_key' => $captcha['key'],
            'question' => $captcha['question']
        ]);
    }
} 