@extends('mail.minty')

@section('content')

	@include('beautymail::templates.minty.contentStart')
		<tr>
			<td width="100%" height="5"></td>
		</tr>
		<tr>
			<td class="paragraph">
				Hello ,
			</td>
		</tr>
		<tr>
			<td width="100%" height="25"></td>
		</tr>
		<tr>
			<td class="paragraph">
				Welcome to Ni-kshay setu!
			</td>
		</tr>
		<tr>
			<td class="paragraph">
				Thank you for showing interest and become Ni-kshay setu member.
			</td>
		</tr>
		<tr>
			<td width="100%" height="35"></td>
		</tr>
		<tr>
			<td class="paragraph">
				Best Regards,
			</td>
		</tr>

		<tr>
			<td class="paragraph">
				Ni-kshay setu
			</td>
		</tr>
		<tr>
			<td width="100%" height="25"></td>
		</tr>
	@include('beautymail::templates.minty.contentEnd')

@stop