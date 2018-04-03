<?php

namespace App\Traits;

trait StandardValidationMessages
{
  /**
   * Map validation rules to errors.
   *
   * @return array
   */
  public function messages()
  {
    return [
      'required' => 'This field is required.',
      'date_format' => 'This isn\'t the right format.',
      'integer' => 'This field should be a number.',
    ];
  }
}
