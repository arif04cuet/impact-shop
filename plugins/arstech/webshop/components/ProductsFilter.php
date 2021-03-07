<?php

namespace Arstech\Webshop\Components;

use Cms\Classes\ComponentBase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use OFFLINE\Mall\Classes\CategoryFilter\Filter;
use OFFLINE\Mall\Classes\CategoryFilter\QueryString;
use OFFLINE\Mall\Classes\CategoryFilter\RangeFilter;
use OFFLINE\Mall\Classes\CategoryFilter\SetFilter;
use OFFLINE\Mall\Classes\Queries\PriceRangeQuery;
use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\Category;
use OFFLINE\Mall\Models\Currency;
use OFFLINE\Mall\Models\Product;
use OFFLINE\Mall\Models\Property;

class ProductsFilter extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'ProductsFilter Component',
            'description' => 'Webshop products filter'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function init()
    {
        if ((bool)$this->property('includeSliderAssets') || 1) {
            $this->addJs('https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.0.3/nouislider.min.js');
            $this->addCss('https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.0.3/nouislider.min.css');
        }
        $this->money = app(Money::class);
    }

    public function onRun()
    {
        $this->setData();
    }

    public function setData()
    {

        $course_place = DB::table('offline_mall_properties')
            ->join('offline_mall_property_values', 'offline_mall_properties.id', '=', 'offline_mall_property_values.property_id')
            ->select('offline_mall_property_values.value', 'slug', DB::raw('COUNT(product_id) AS total'))
            ->where('slug', 'course-place')
            ->groupBy('value', 'slug')
            ->get()
            ->toArray();


        $course_points = DB::table('offline_mall_properties')
            ->join('offline_mall_property_values', 'offline_mall_properties.id', '=', 'offline_mall_property_values.property_id')
            ->select('offline_mall_property_values.value', 'slug', DB::raw('COUNT(product_id) AS total'))
            ->where('slug', 'course-points')
            ->groupBy('value', 'slug')
            ->get()
            ->toArray();

        $categories = Category::withCount(['products'])->get();

        $currency = Currency::defaultCurrency();

        $priceRange = (new PriceRangeQuery($categories, $currency))->query()->first();


        $money = app(Money::class);
        $max = $money->round($priceRange->max, $currency->decimals);
        $min = $money->round($priceRange->min, $currency->decimals);



        $filters = [
            'course_place' => $course_place,
            'course_points' => $course_points,
            'categories' => $categories,
            'price_range' => [$min, $max]
        ];


        $this->page['filters'] = $filters;
    }

    public function onFilterUpdate()
    {

        $data = post('filter', []);
        $search = post('q');

        $products = Product::published()
            ->when(isset($data['price']['min']) && !empty($data['price']['min']), function ($q) use ($data) {

                return $q->whereHas('prices', function ($pq) use ($data) {
                    $min = number_format($data['price']['min']) * 100;
                    $max = number_format($data['price']['max']) * 100;
                    $pq->whereBetween('price', [$min, $max]);
                });
            })->when(isset($data['category']), function ($q) use ($data) {
                return $q->whereHas('categories', function ($cq) use ($data) {
                    $cq->whereIn('offline_mall_categories.id', $data['category']);
                });
            })->when(isset($data['course-place']), function ($q) use ($data) {

                return $q->whereHas('property_values', function ($q) use ($data) {
                    $q->whereHas('property', function ($q) use ($data) {
                        $q->where('slug', 'course-place');
                    })->whereIn('value', $data['course-place']);
                });
            })->when(isset($data['course-points']), function ($q) use ($data) {

                return $q->whereHas('property_values', function ($q) use ($data) {
                    $q->whereHas('property', function ($q) use ($data) {
                        $q->where('slug', 'course-points');
                    })->whereIn('value', $data['course-points']);
                });
            })->when($search, function ($q) use ($search) {
                return $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('description_short', 'like', "%{$search}%");
            });

        // traceLog($products->toSql());
        // traceLog($products->getBindings());

        $this->page['items'] = $products->get();
    }
    public function onSetFilter()
    {


        $data = collect(post('filter', []));
        if ($data->count() < 1) {
            return $this->replaceFilter(collect([]));
        }

        $properties = Property::whereIn('slug', $data->keys())->get();
        $filter     = $data->mapWithKeys(function ($values, $id) use ($properties) {
            $property = Filter::isSpecialProperty($id) ? $id : $properties->where('slug', $id)->first();
            if (
                is_array($values)
                && array_key_exists('min', $values)
                && array_key_exists('max', $values)
            ) {
                if ($values['min'] === '' && $values['max'] === '') {
                    return [];
                }

                return [
                    $id => new RangeFilter(
                        $property,
                        [
                            $values['min'] ?? null,
                            $values['max'] ?? null,
                        ]
                    ),
                ];
            }

            // Remove empty set values
            $values = array_filter(array_wrap($values));

            return count($values) ? [$id => new SetFilter($property, $values)] : [];
        });


        return $this->replaceFilter($filter);
    }

    protected function replaceFilter(Collection $filter)
    {

        $sortOrder = 'latest';

        return [
            'filter'      => $filter,
            'sort'        => $sortOrder,
            'queryString' => (new QueryString())->serialize($filter, $sortOrder),
        ];
    }
}
