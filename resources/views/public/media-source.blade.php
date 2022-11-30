<div class="py-4">
	<h4 class="text-2xl font-bold mb-4">Emby Settings</h4>

	<form action="/dmp-emby/settings" method="post">
		@csrf
		@method('put')
		<div class="mb-5">
			<label for="emby-server-url" class="block mb-2 font-bold"
				>Emby IP Address</label
			>
			<input
				type="text"
				class="w-full mb-2"
				id="emby-server-url"
				aria-describedby="emby-server-urlHelp"
				name="emby_ip_address"
				value="{{ $options['emby_ip_address'] }}"
				required
			/>
			<div id="emby-server-urlHelp" class="text-gray-400 text-sm">Ex: localhost, 10.0.0.32</div>
		</div>

		<div class="mb-5">
			<label for="emby-server-port" class="block mb-2 font-bold"
				>Emby API Token</label
			>
			<input
				type="text"
				class="w-full mb-2"
				id="emby-server-port"
				aria-describedby="emby-server-portHelp"
				name="emby_token"
				value="{{ $options['emby_token'] }}"
				required
			/>
			<div id="emby-server-portHelp" class="text-gray-400 text-sm"></div>
		</div>

		<div class="mb-5">
			<label for="emby-use-ssl" class="text-gray-300 block mb-2 font-bold flex items-center">
				<input
					type="checkbox"
					class="text-black"
					id="emby-use-ssl"
					aria-describedby="plex-use-sslHelp"
					name="emby_use_ssl"
					value="1"
					@checked(old('emby_use_ssl', $options['emby_use_ssl']))
				/>
				<span class="ml-2">Use SSL for Emby IP address</span>
			</label>
			<div id="plex-use-sslHelp" class="text-gray-400 text-sm"></div>
		</div>

		<button type="submit" class="btn text-black bg-gray-300 text-md px-3 py-1 rounded-sm hover:bg-gray-100">Save Emby Settings</button>
	</form>
</div>
