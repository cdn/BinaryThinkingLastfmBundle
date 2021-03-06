<?php

namespace BinaryThinking\LastfmBundle\Lastfm\Client\Method;

use BinaryThinking\LastfmBundle\Lastfm\Client\LastfmAPIClient;
use BinaryThinking\LastfmBundle\Lastfm\Model as LastfmModel;

/**
 * AlbumMethodsClient
 *
 * @author Karol Sójko <karolsojko@gmail.com>
 */
class AlbumMethodsClient extends LastfmAPIClient 
{
    
    /**
     * Get the metadata and tracklist for an album on Last.fm using the album name or a musicbrainz id.
     * 
     * @param string $artist the artist name
     * @param string $album the album name
     * @param string $mbid the musicbrainz id for the album
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     * @param string $username The username for the context of the request
     * @param string $lang The language to return the biography in, expressed as an ISO 639 alpha-2 code.
     */
    public function getInfo($artist, $album, $mbid = null, $autocorrect = true, $username = null, $lang = null)
    {
        $response = $this->call(array(
            'method' => 'album.getInfo',
            'artist' => $artist,
            'album' => $album,
            'mbid' => $mbid,
            'autocorrect' => $autocorrect,
            'username' => $username,
            'lang' => $lang
        ));
        
        $album = null;
        if(!empty($response->album)){
            $album = LastfmModel\Album::createFromResponse($response->album);
        }
        
        return $album;
    }
    
    /**
     * Get the tags applied by an individual user to an album on Last.fm. 
     * To retrieve the list of top tags applied to an album by all users 
     * use getTopTags.
     * 
     * @param string $artist the artist name
     * @param string $album the album name
     * @param string $user If called in non-authenticated mode you must specify the user to look up
     * @param string $mbid the musicbrainz id for the album
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     */
    public function getTags($artist, $album, $user, $mbid = null, $autocorrect = true)
    {
        $response = $this->call(array(
            'method' => 'album.getTags',
            'artist' => $artist,
            'album' => $album,
            'mbid' => $mbid,
            'autocorrect' => $autocorrect,
            'user' => $user
        ));
        
        $tags = array();
        if(!empty($response->tags->tag)){
            foreach($response->tags->tag as $albumTag){
                $tag = LastfmModel\Tag::createFromResponse($albumTag);
                $tags[$tag->getName()] = $tag;
            }
        }
        
        return $tags;
    }
    
    /**
     * Get the top tags for an album on Last.fm, ordered by popularity.
     * 
     * @param string $artist the artist name
     * @param string $album the album name
     * @param string $mbid the musicbrainz id for the album
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     */
    public function getTopTags($artist, $album, $mbid = null, $autocorrect = true)
    {
        $response = $this->call(array(
            'method' => 'album.getTopTags',
            'artist' => $artist,
            'album' => $album,
            'mbid' => $mbid,
            'autocorrect' => $autocorrect
        ));
        
        $tags = array();
        if(!empty($response->toptags->tag)){
            foreach($response->toptags->tag as $topTag){
                $tag = LastfmModel\Tag::createFromResponse($topTag);
                $tags[$tag->getName()] = $tag;
            }
        }
        
        return $tags;
    }
    
    /**
     * Search for an album by name. Returns album matches sorted by relevance.
     * 
     * @param string $album the album name
     * @param int $limit the number of results to fetch per page. Defaults to 30.
     * @param int $page the page number to fetch. Defaults to first page.
     */
    public function search($album, $limit = null, $page = null)
    {
        $response = $this->call(array(
            'method' => 'album.search',
            'album' => $album,
            'limit' => $limit,
            'page' => $page
        ));
        
        $albums = array();
        if(!empty($response->results->albummatches->album)){
            foreach($response->results->albummatches->album as $albumMatch){
                $album = LastfmModel\Album::createFromResponse($albumMatch);
                $albums[$album->getId()] = $album;
            }            
        }
        
        return $albums;
    }
    
    /**
     * Get shouts for this album.
     * 
     * @param string $artist the artist name
     * @param string $album the album name
     * @param int $limit the number of results to fetch per page. Defaults to 30.
     * @param int $page the page number to fetch. Defaults to first page.
     * @param string $mbid the musicbrainz id for the album
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     */
    public function getShouts($artist, $album, $limit = null, $page = null, $mbid = null, $autocorrect = true)
    {
        $response = $this->call(array(
            'method' => 'album.getShouts',
            'artist' => $artist,
            'album' => $album,
            'mbid' => $mbid,
            'limit' => $limit,
            'page' => $page,
            'autocorrect' => $autocorrect
        ));
        
        $shouts = array();
        if(!empty($response->shouts->shout)){
            foreach($response->shouts->shout as $albumShouts){
                $shout = LastfmModel\Shout::createFromResponse($albumShouts);
                $shouts[] = $shout;
            }            
        }
        
        return $shouts;        
    }
    
    /**
     * Get a list of Buy Links for a particular Album. It is required that you supply either the artist and album params or the mbid param.
     * 
     * @param string $artist the artist name
     * @param string $album the album name
     * @param string $mbid the musicbrainz id for the album
     * @param bool $autocorrect transform misspelled artist names into correct artist names
     * @param string $country a country name, as defined by the ISO 3166-1 country names standard.
     */
    public function getBuylinks($artist, $album, $mbid = null, $autocorrect = true, $country = null)
    {
        $response = $this->call(array(
            'method' => 'album.getBuylinks',
            'artist' => $artist,
            'album' => $album,
            'autocorrect' => $autocorrect,
            'mbid' => $mbid,
            'country' => $country
        ));
        
        $affiliations = array();
        if(!empty($response->affiliations->physicals->affiliation)){
            foreach($response->affiliations->physicals->affiliation as $physicalAffiliation){
                $affiliations['physicals'][] = LastfmModel\Affiliation::createFromResponse($physicalAffiliation);
            }
        }
        if(!empty($response->affiliations->downloads->affiliation)){
            foreach($response->affiliations->downloads->affiliation as $downloadAffiliation){
                $affiliations['downloads'][] = LastfmModel\Affiliation::createFromResponse($downloadAffiliation);
            }
        }
        
        return $affiliations;
    }
    
}
