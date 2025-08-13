<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Rules\AvailableTimeSlot;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    private ?Service $service = null;

    public function authorize(): bool
    {
        return $this->user()->isCustomer();
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'start_time' => [
                'required',
                'date',
                'after:now',
                $this->getAvailableTimeSlotRule(),
            ],
        ];
    }

    private function getAvailableTimeSlotRule(): AvailableTimeSlot
    {
        return new AvailableTimeSlot($this->getService());
    }

    private function getService(): Service
    {
        if (!$this->service) {
            $this->service = Service::findOrFail($this->input('service_id'));
        }
        return $this->service;
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Please select a service.',
            'service_id.exists' => 'The selected service is invalid.',
            'start_time.required' => 'Please select a booking time.',
            'start_time.date' => 'Invalid date format.',
            'start_time.after' => 'Booking time must be in the future.',
        ];
    }
}