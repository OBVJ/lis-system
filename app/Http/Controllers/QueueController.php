<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\LabRequest;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index()
    {
        // Waiting: patients with pending requests (no sample collected)
        $waiting = $this->getPatientsInStatus('pending');

        // Sample Collected: patients with collected requests (sample taken)
        $sampleCollected = $this->getPatientsInStatus('sample_collected');

        // In Progress: patients with in_progress requests (results being entered)
        $inProgress = $this->getPatientsInStatus('in_progress');

        // Ready: patients with completed requests (results done)
        $ready = $this->getPatientsInStatus('completed');

        // Printed/Delivered: patients with delivered requests
        $printedDelivered = $this->getPatientsInStatus('delivered');

        return view('queue.index', compact(
            'waiting', 'sampleCollected', 'inProgress', 'ready', 'printedDelivered'
        ));
    }

    private function getPatientsInStatus(string $status)
    {
        $statuses = [$status];

        // Handle legacy status mappings
        if ($status === 'sample_collected') {
            $statuses = ['collected', 'sample_collected'];
        }

        return Patient::whereHas('requests', function ($q) use ($statuses) {
            $q->whereIn('status', $statuses);
        })
        ->with(['requests' => function ($q) use ($statuses) {
            $q->whereIn('status', $statuses)->latest()->with('payment');
        }])
        ->withCount(['requests as request_count' => function ($q) use ($statuses) {
            $q->whereIn('status', $statuses);
        }])
        ->get();
    }

    private function getPatientsWithSampleCollected()
    {
        return Patient::whereHas('requests', function ($q) {
            $q->where('status', 'collected')
              ->whereHas('samples', function ($sq) {
                  $sq->where('status', 'collected');
              })
              ->whereHas('items', function ($iq) {
                  $iq->where('status', 'pending');
              });
        })
        ->withCount(['requests as request_count' => function ($q) {
            $q->where('status', 'in_progress');
        }])
        ->get();
    }

    private function getPatientsInTesting()
    {
        return Patient::whereHas('requests', function ($q) {
            $q->where('status', 'in_progress')
              ->whereHas('samples', function ($sq) {
                  $sq->where('status', 'collected');
              });
        })
        ->withCount(['requests as request_count' => function ($q) {
            $q->where('status', 'in_progress');
        }])
        ->get();
    }

    private function getPatientsCompleted()
    {
        return Patient::whereHas('requests', function ($q) {
            $q->where('status', 'completed');
        })
        ->withCount(['requests as request_count' => function ($q) {
            $q->where('status', 'completed');
        }])
        ->get();
    }

    private function getPatientsDelivered()
    {
        // Patients whose requests are explicitly marked as delivered
        return Patient::whereHas('requests', function ($q) {
            $q->where('status', 'delivered');
        })
        ->withCount(['requests as request_count' => function ($q) {
            $q->where('status', 'delivered');
        }])
        ->get();
    }
}
