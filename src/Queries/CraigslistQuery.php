<?php namespace JobApis\Jobs\Client\Queries;

class CraigslistQuery extends AbstractQuery
{
    /**
     * location
     *
     * The search location. Must be one of those supported by Craigslist
     *  https://sites.google.com/site/clsiteinfo/city-site-code-sort
     *
     * @var string
     */
    protected $location;

    /**
     * format
     *
     * Format: must be "rss".
     *
     * @var string
     */
    protected $format;

    /**
     * query
     *
     * The search query.
     *
     * @var string
     */
    protected $query;

    /**
     * s
     *
     * Starting result. Always uses 100 per page.
     *
     * @var integer
     */
    protected $s;

    /**
     * searchNearby
     *
     * Should the results include nearby areas
     *
     * @var boolean
     */
    protected $searchNearby;

    /**
     * is_internship
     *
     * @var boolean
     */
    protected $is_internship;

    /**
     * is_nonprofit
     *
     * @var boolean
     */
    protected $is_nonprofit;

    /**
     * is_telecommuting
     *
     * @var boolean
     */
    protected $is_telecommuting;

    /**
     * employment_type
     *
     * Valid options:
     *  1: full time
     *  2: part time
     *  3: contract
     *  4: employee's choice
     *
     * @var integer
     */
    protected $employment_type;

    /**
     * Get baseUrl
     *
     * @return  string Value of the base url to this api
     */
    public function getBaseUrl()
    {
        return 'http://'.$this->location.'.craigslist.org/search/jjj';
    }

    /**
     * Get keyword
     *
     * @return  string Attribute being used as the search keyword
     */
    public function getKeyword()
    {
        return $this->query;
    }

    /**
     * Default parameters
     *
     * @return array
     */
    protected function defaultAttributes()
    {
        return [
            'format' => 'rss',
        ];
    }

    /**
     * Required parameters
     *
     * @return array
     */
    protected function requiredAttributes()
    {
        return [
            'format',
            'location',
        ];
    }
}
