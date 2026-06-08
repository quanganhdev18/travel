<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function search(Request $request)
    {
        $banners = Banner::where('is_active', true)->where('position', 'top')->get();
        $destinations = Destination::all();
        $categories = Category::all();

        $query = Ticket::with(['destination', 'ticket_options']);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhereHas('destination', function ($qDest) use ($keyword) {
                        $qDest->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        if ($request->filled('sort')) {
            if ($request->sort == 'price_asc' || $request->sort == 'price_desc') {
                $direction = $request->sort == 'price_asc' ? 'asc' : 'desc';
                // Sắp xếp theo giá thấp nhất của option
                $query->select('tickets.*')
                    ->leftJoin('ticket_options', 'tickets.id', '=', 'ticket_options.ticket_id')
                    ->groupBy(
                        'tickets.id', 'tickets.destination_id', 'tickets.title', 'tickets.slug',
                        'tickets.description', 'tickets.provider_name', 'tickets.cancellation_policy',
                        'tickets.created_at', 'tickets.updated_at', 'tickets.deleted_at'
                    )
                    ->orderByRaw("MIN(ticket_options.price) $direction");
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $tickets = $query->paginate(6)->withQueryString();

        if ($request->ajax()) {
            return view('frontend.tickets._results', compact('tickets'))->render();
        }

        return view('frontend.tickets.search', compact('tickets', 'destinations', 'categories', 'banners'));
    }
}
