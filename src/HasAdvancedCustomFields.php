<?php 

namespace Lumenpress\Acf;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasAdvancedCustomFields extends HasMany
{
    /**
     * Create a new has one or many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return void
     */
    public function __construct(Model $parent)
    {
        $instance = $this->newRelatedInstance($parent);

        parent::__construct(
            $instance->newQuery(), 
            $parent,
            $this->getForeignKey($parent), 
            $parent->getKeyName()
        );
    }

    public function whereKeyIs($key)
    {
        return $this->query->where($this->getKeyColumnName(), $key);
    }

    /**
     * [getAcfKey description]
     * @return [type] [description]
     */
    protected function getKeyColumnName()
    {
        return $this->parent->getTable() == 'options' ? 'option_key' : 'meta_key';
    }

    /**
     * [getAcfRelated description]
     * @return [type] [description]
     */
    protected function newRelatedInstance($parent)
    {
        $class = \Lumenpress\Acf\Models\OptionField::class;

        switch ($parent->getTable()) {
            case 'posts':
                $class = \Lumenpress\Acf\Models\PostField::class;
                break;
            case 'terms':
                $class = \Lumenpress\Acf\Models\TermField::class;
            case 'users':
                $class = \Lumenpress\Acf\Models\UserField::class;
            case 'comments':
                $class = \Lumenpress\Acf\Models\CommentField::class;
        }

        return tap(new $class, function ($instance) use ($parent) {
            if (! $instance->getConnectionName()) {
                $instance->setConnection($parent->getConnectionName());
            }
        });
    }

    protected function getForeignKey($parent)
    {
        switch ($parent->getTable()) {
            case 'posts':
                return 'postmeta.post_id';
            case 'terms':
                return 'termmeta.term_id';
            case 'users':
                return 'usermeta.user_id';
            case 'comments':
                return 'commentmeta.comment_id';
            default:
                return 'options';
        }
    }
}
