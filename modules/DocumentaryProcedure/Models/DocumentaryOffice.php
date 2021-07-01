<?php

    namespace Modules\DocumentaryProcedure\Models;

    use App\Models\Tenant\ModelTenant;
    use App\Models\Tenant\User;
    use Hyn\Tenancy\Traits\UsesTenantConnection;
    use Illuminate\Database\Eloquent\Builder;

    /**
     * Modules\DocumentaryProcedure\Models\DocumentaryOffice
     *
     * @property-read mixed $active
     * @method static Builder|DocumentaryOffice newModelQuery()
     * @method static Builder|DocumentaryOffice newQuery()
     * @method static Builder|DocumentaryOffice query()
     * @mixin \Eloquent
     */
    class DocumentaryOffice extends ModelTenant {
        protected $table = 'documentary_offices';
        use UsesTenantConnection;

        protected $fillable = [
            'description',
            'active',
            'name',
            'parent_id',
            'order',
        ];

        /**
         * @return string
         */
        public function getDescription()
         {
            return $this->description;
        }

        /**
         * @param string $description
         *
         * @return DocumentaryOffice
         */
        public function setDescription( $description)
        : DocumentaryOffice {
            $this->description = $description;
            return $this;
        }

        /**
         * @return string
         */
        public function getName()
         {
            return $this->name;
        }

        /**
         * @param string $name
         *
         * @return DocumentaryOffice
         */
        public function setName( $name)
        : DocumentaryOffice {
            $this->name = $name;
            return $this;
        }

        /**
         * @param int $parent_id
         *
         * @return DocumentaryOffice
         */
        public function setParentId($parent_id)
        : DocumentaryOffice {
            if($parent_id == $this->id) $parent_id = 0;
            $this->parent_id = (int) $parent_id;
            return $this;
        }

        /**
         * @return int
         */
        public function getOrder()
         {
            return $this->order;
        }

        /**
         * @param int $order
         *
         * @return DocumentaryOffice
         */
        public function setOrder($order)
        : DocumentaryOffice {
            $this->order = (int)$order;
            return $this;
        }

        /**
         * @param $value
         *
         * @return bool
         */
        public function getActiveAttribute($value) {
            return $value ? true : false;
        }

        /**
         * @param false $extended
         *
         * @return array
         */
        public function getCollectionData($extended = false, $level = 0) {

            $data = $this->toArray();
            $parent = [];
            if ($this->getParentId() != 0) {
                $parent = self::find($this->getParentId());
                $parent = $parent->getCollectionData();
            }

            $data['parent'] = $parent;
            $data['child'] = $this->getChildren($level);
            $data['rel_user_to_documentary_offices'] =
                RelUserToDocumentaryOffices::where('documentary_office_id',
                                                   $this->id)
                                           ->get()
                                           ->transform(function ($row) {
                                               return $row->getCollectionData();
                                           });
            $data['users'] = RelUserToDocumentaryOffices::
            where('documentary_office_id', $this->id)->where('active', 1)
                                                        ->get()->pluck('user_id');
            $user = User::wherein('id',$data['users'])->first();
            $data['user'] = null;
            if(!empty($user)){
                $data['user'] = $user->getCollectionData();
            }

            // $data['print_name'] = $this->id." - ".$this->name;
            $data['print_name'] = $this->name;

            if ($extended === true) {

                $data['documentary_files_archives'] =
                    DocumentaryFilesArchives::where('documentary_office_id',
                                                    $this->id)
                                            ->get()
                                            ->transform(function ($row) {
                                                return $row->getCollectionData();
                                            });


                $data['documentary_file_offices'] =
                    DocumentaryFileOffice::where('documentary_office_id', $this->id)
                                         ->get()
                                         ->transform(function ($row) {
                                             return $row->getCollectionData();
                                         });
            }


            return $data;
        }

        /**
         * @return int
         */
        public function getParentId()
         {
            return (int)$this->parent_id;
        }

        public function getChildren($level = 0) {
            if($this->parent_id == 0) return [];
            if($level > 0) return [];
            $childs = self::where('parent_id', $this->id)->get();
            if(!empty($childs)){
                $childs = $childs->transform(function ($row) use($level) {
                    return $row->getCollectionData(false, $level +1 );
                });
            }
            return $childs;
        }

        public function getBack() {
            $parent = $this->getParentId();
            $work = self::where('id', '<', $this->id);
            $lastest = $work->max('id');
            if ($parent != 0) {
                $temp_work = self::where('id', '<', $this->id)->where('parent_id', $parent)->max('id');
                if($temp_work == null){
                    $temp_work = self::where('id', '<',$parent)->max('id');
                }
                $lastest = ($temp_work == null)? $lastest:$temp_work;
            }
            return $lastest;
        }
    }
