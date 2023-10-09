<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Http\Requests\CreateLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Link;
use App\Pixel;
use App\Space;
use App\Stat;
use App\Traits\LinkTrait;
use App\Traits\UserFeaturesTrait;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Csv as CSV;

class LinksController extends Controller
{
    use LinkTrait, UserFeaturesTrait;

    public function index(Request $request)
    {
        $user = Auth::user();

        // Get the user's spaces
        $spaces = Space::where('user_id', $user->id)->get();

        $userFeatures = $this->getFeatures($user);

        // Get the user's domains
        $domains = Domain::whereIn('user_id', $user->can('globalDomains', ['App\Link', $userFeatures['option_global_domains']]) ? [0, $user->id] : [$user->id])->when(config('settings.short_domain'), function($query) { return $query->orWhere('id', '=', config('settings.short_domain')); })->orderBy('name')->get();

        // Get the user's pixels
        $pixels = Pixel::where('user_id', $user->id)->get();

        $search = $request->input('search');
        $space = $request->input('space');
        $domain = $request->input('domain');
        $status = $request->input('status');
        $by = $request->input('by');

        if ($request->input('sort') == 'min') {
            $sort = ['clicks', 'asc', 'min'];
        } elseif ($request->input('sort') == 'max') {
            $sort = ['clicks', 'desc', 'max'];
        } elseif ($request->input('sort') == 'asc') {
            $sort = ['id', 'asc', 'asc'];
        } else {
            $sort = ['id', 'desc', 'desc'];
        }

        // If there's no toast notification
        if (session('toast') == false) {
            // Set the session to a countable object
            session(['toast' => []]);
        }

        $links = Link::with('domain', 'space')->where('user_id', $user->id)
            ->when($domain, function($query) use ($domain) {
                return $query->searchDomain($domain);
            })
            ->when($space, function($query) use ($space) {
                return $query->searchSpace($space);
            })
            ->when($status, function($query) use ($status) {
                if($status == 1) {
                    return $query->searchActive();
                } elseif($status == 2) {
                    return $query->searchExpired();
                } else {
                    return $query->searchDisabled();
                }
            })
            ->when($search, function($query) use ($search, $by) {
                if($by == 'url') {
                    return $query->searchUrl($search);

                } elseif ($by == 'alias') {
                    return $query->searchAlias($search);
                }
                return $query->searchTitle($search);
            })
            ->orderBy($sort[0], $sort[1])
            ->paginate(config('settings.paginate'))
            ->appends(['search' => $search, 'domain' => $domain, 'space' => $space, 'by' => $by, 'sort' => $sort[2]]);

        return view('links.content', ['view' => 'list', 'links' => $links, 'spaces' => $spaces, 'domains' => $domains, 'pixels' => $pixels, 'userFeatures' => $userFeatures, 'user' => $user]);
    }

    public function linksExport(Request $request)
    {
        $user = Auth::user();

        $search = $request->input('search');
        $space = $request->input('space');
        $domain = $request->input('domain');
        $status = $request->input('status');
        $by = $request->input('by');

        if ($request->input('sort') == 'min') {
            $sort = ['clicks', 'asc'];
        } elseif ($request->input('sort') == 'max') {
            $sort = ['clicks', 'desc'];
        } elseif ($request->input('sort') == 'asc') {
            $sort = ['id', 'asc'];
        } else {
            $sort = ['id', 'desc'];
        }

        $links = Link::with('domain', 'space')
            ->where('user_id', $user->id)
            ->when($domain, function($query) use ($domain) {
                return $query->searchDomain($domain);
            })
            ->when($space, function($query) use ($space) {
                return $query->searchSpace($space);
            })
            ->when($status, function($query) use ($status) {
                if($status == 1) {
                    return $query->searchActive();
                } elseif($status == 2) {
                    return $query->searchExpired();
                } else {
                    return $query->searchDisabled();
                }
            })
            ->when($search, function($query) use ($search, $by) {
                if($by == 'url') {
                    return $query->searchUrl($search);

                } elseif ($by == 'alias') {
                    return $query->searchAlias($search);
                }
                return $query->searchTitle($search);
            })
            ->orderBy($sort[0], $sort[1])
            ->get();

        $content = CSV\Writer::createFromFileObject(new \SplTempFileObject);

        // Generate the header
        $content->insertOne([__('Type'), __('Links')]);
        $content->insertOne([__('Date'), Carbon::now()->tz($user->timezone ?? config('app.timezone'))->format(__('Y-m-d')) . ' ' . Carbon::now()->tz($user->timezone ?? config('app.timezone'))->format('H:i:s') . ' (' . CarbonTimeZone::create($user->timezone ?? config('app.timezone'))->toOffsetName() . ')']);
        $content->insertOne([__(' ')]);

        // Generate the content
        $content->insertOne([__('Short'), __('Original'), __('Alias'), __('Title'), __('Created at')]);
        foreach ($links as $link) {
            $content->insertOne([(($link->domain->name ?? config('app.url')) . '/' . $link->alias), $link->url, $link->alias, $link->title, $link->created_at->tz($user->timezone ?? config('app.timezone'))->format(__('Y-m-d'))]);
        }

        return response((string) $content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . formatTitle([__('Links'), config('settings.title')]) . '.csv"',
        ]);
    }

    public function linksEdit($id)
    {
        $user = Auth::user();

        // Get the user's spaces
        $spaces = Space::where('user_id', $user->id)->get();

        // Get the user's domains
        $domains = Domain::where('user_id', $user->id)->get();

        // Get the user's pixels
        $pixels = Pixel::where('user_id', $user->id)->get();

        $link = Link::where([['id', '=', $id], ['user_id', '=', $user->id]])->firstOrFail();

        return view('links.content', ['view' => 'edit', 'spaces' => $spaces, 'domains' => $domains, 'pixels' => $pixels, 'link' => $link]);
    }

    public function createLink(CreateLinkRequest $request)
    {
        $user = Auth::user();

        if ($request->multi_link) {

            $links = $this->linksCreate($request);

            return redirect()->back()->with('toast', Link::where('user_id', '=', $user->id)->orderBy('id', 'desc')->limit(count($links))->get());
        } else {
            $this->linkCreate($request);

            return redirect()->back()->with('toast', Link::where('user_id', '=', $user->id)->orderBy('id', 'desc')->limit(1)->get());
        }
    }

    public function updateLink(UpdateLinkRequest $request, $id)
    {
        $user = Auth::user();

        $link = Link::where([['id', '=', $id], ['user_id', '=', $user->id]])->firstOrFail();

        $this->linkUpdate($request, $link);

        return redirect()->route('links.edit', $id)->with('success', __('Settings saved.'));
    }

    public function deleteLink(Request $request, $id)
    {
        $user = Auth::user();

        $link = Link::where([['id', '=', $id], ['user_id', '=', $user->id]])->firstOrFail();

        $link->delete();

        return redirect()->route('links')->with('success', __(':name has been deleted.', ['name' => str_replace(['http://', 'https://'], '', (($link->domain->name ?? config('app.url')) . '/' . $link->alias))]));
    }
}
