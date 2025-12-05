<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Collection;
use App\Models\Egi;
use App\Enums\User\MerchantUserTypeEnum;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * @Oracode Controller: Company Home Page Management
 * 🎯 Purpose: Handle company's public storefront and catalog pages
 * 🛡️ Security: Public access with business profile visibility
 * 🧱 Core Logic: Display company's products, collections, and business info
 * 🎨 Palette: Corporate Blue (#1E3A5F), Business Gold (#C9A227), Success Green (#2D7D46)
 *
 * @package App\Http\Controllers
 * @author Fabio Cherici & AI Assistant (FlorenceEGI Company System)
 * @version 1.0.0 (FlorenceEGI MVP Company Storefront)
 * @date 2025-01-XX
 */
class CompanyHomeController extends Controller {
    /**
     * @Oracode Method: Display Company Index Page
     * 🎯 Purpose: List all companies with filtering and search capabilities
     * 📤 Output: Companies index view with pagination and filters
     */
    public function index(Request $request): View {
        $query = $request->input('query');
        $sort = $request->input('sort', 'latest'); // 'latest', 'most_products', 'most_sales'

        $companies = User::query()
            ->where('usertype', MerchantUserTypeEnum::COMPANY->value)
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%');
            })
            ->withCount(['createdEgis as products_count' => function ($productQuery) {
                $productQuery->where('is_published', true);
            }])
            ->with([
                'createdEgis' => function ($egiQuery) {
                    $egiQuery->where('is_published', true)
                        ->take(3);
                },
            ])
            ->when($sort === 'most_products', function ($q) {
                $q->orderByDesc('products_count');
            })
            ->when($sort === 'latest', function ($q) {
                $q->latest();
            })
            ->paginate(20);

        return view('company.index', compact(
            'companies',
            'query',
            'sort'
        ));
    }

    /**
     * @Oracode Method: Display Company Home Page
     * 🎯 Purpose: Show company's main storefront with stats and featured products
     * 📤 Output: Company home view with business stats and featured content
     */
    public function home(int $id, Request $request): View {
        $company = User::findOrFail($id);

        // Verifica che l'utente sia di tipo company
        if ($company->usertype !== MerchantUserTypeEnum::COMPANY->value) {
            abort(404, 'User is not a company');
        }

        // Statistiche aziendali
        $stats = $this->getCompanyStats($company);

        // Prodotti in evidenza (ultimi 8 EGI pubblicati dalla company)
        $featuredProducts = Egi::where('user_id', $company->id)
            ->where('is_published', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Collezioni della company (ultime 6)
        $companyCollections = Collection::where('creator_id', $company->id)
            ->whereIn('status', ['published', 'active'])
            ->withCount('egis')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('company.home', [
            'company' => $company,
            'stats' => $stats,
            'featuredProducts' => $featuredProducts,
            'companyCollections' => $companyCollections,
            'activeTab' => 'overview',
        ]);
    }

    /**
     * @Oracode Method: Display Company Catalog Page
     * 🎯 Purpose: Show full product catalog with filters, search, and pagination
     * 📤 Output: Catalog view with product grid and filtering options
     */
    public function catalog(int $id, Request $request): View {
        $company = User::findOrFail($id);

        if ($company->usertype !== MerchantUserTypeEnum::COMPANY->value) {
            abort(404, 'User is not a company');
        }

        $search = $request->input('search');
        $category = $request->input('category');
        $sort = $request->input('sort', 'newest');

        // Query base per i prodotti
        $query = Egi::where('user_id', $company->id)
            ->where('is_published', true);

        // Filtro ricerca
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro categoria (se implementato)
        if ($category) {
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        // Ordinamento
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_low':
                $query->orderBy('egi_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('egi_price', 'desc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Paginazione
        $products = $query->paginate(16);

        // Categorie disponibili per i filtri
        $categories = collect(); // Placeholder - implementare se ci sono categorie

        // Stats per header
        $stats = $this->getCompanyStats($company);

        return view('company.catalog', [
            'company' => $company,
            'products' => $products,
            'categories' => $categories,
            'stats' => $stats,
            'activeTab' => 'catalog',
        ]);
    }

    /**
     * @Oracode Method: Display Company Collections Page
     * 🎯 Purpose: Show all collections owned by the company
     * 📤 Output: Collections grid view
     */
    public function collections(int $id, Request $request): View {
        $company = User::findOrFail($id);

        if ($company->usertype !== MerchantUserTypeEnum::COMPANY->value) {
            abort(404, 'User is not a company');
        }

        $collections = Collection::where('creator_id', $company->id)
            ->whereIn('status', ['published', 'active'])
            ->withCount('egis')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $stats = $this->getCompanyStats($company);

        return view('company.collections', [
            'company' => $company,
            'collections' => $collections,
            'stats' => $stats,
            'activeTab' => 'collections',
        ]);
    }

    /**
     * @Oracode Method: Display Company About Page
     * 🎯 Purpose: Show company's detailed business information
     * 📤 Output: About page with company bio, contact info, certifications
     */
    public function about(int $id, Request $request): View {
        $company = User::findOrFail($id);

        if ($company->usertype !== MerchantUserTypeEnum::COMPANY->value) {
            abort(404, 'User is not a company');
        }

        $stats = $this->getCompanyStats($company);

        return view('company.about', [
            'company' => $company,
            'stats' => $stats,
            'activeTab' => 'about',
        ]);
    }

    /**
     * @Oracode Method: Get Company Statistics
     * 🎯 Purpose: Calculate and return business statistics
     * 📤 Output: Array with total products, sales, followers, etc.
     */
    private function getCompanyStats(User $company): array {
        // Conteggio prodotti pubblicati
        $totalProducts = Egi::where('user_id', $company->id)
            ->where('is_published', true)
            ->count();

        // Conteggio vendite completate (reservation completed dove l'EGI appartiene alla company)
        $totalSales = Egi::where('user_id', $company->id)
            ->whereHas('reservations', function ($q) {
                $q->where('status', 'completed');
            })
            ->count();

        // Conteggio follower (se implementato)
        $followersCount = 0;
        if (method_exists($company, 'followers')) {
            $followersCount = $company->followers()->count();
        }

        // Conteggio collezioni attive
        $collectionsCount = Collection::where('creator_id', $company->id)
            ->whereIn('status', ['published', 'active'])
            ->count();

        return [
            'total_products' => $totalProducts,
            'total_sales' => $totalSales,
            'followers_count' => $followersCount,
            'collections_count' => $collectionsCount,
        ];
    }
}
