<?php 

namespace Lumenpress\Acf\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Lumenpress\Acf\Models\FieldMeta;

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
        return tap(new FieldMeta, function ($instance) use ($parent) {
            $instance->setTableThroughParentTable($parent->getTable());

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
                return 'options.option_id';
        }
    }
}
