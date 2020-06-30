<div class="m-t-10 m-b-10 p-l-10 p-r-10 p-t-10 p-b-10">
	<div class="row">
		<div class="col-md-12">
{{--			{{ trans('backpack::crud.details_row') }}--}}
			Osobe:
			@foreach($entry->osobe as $osoba)
				<p>JMBG:{{$osoba->id}}, LIB:{{$osoba->lib}}, <strong>{{$osoba->ime}} {{$osoba->prezime}}</strong>, {{$osoba->zvanjeId->naziv}}</p>
				@endforeach
		</div>
	</div>
</div>
<div class="clearfix"></div>